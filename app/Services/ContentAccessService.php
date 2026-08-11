<?php

namespace App\Services;

use App\Models\ContentAccessSession;
use App\Models\Course;
use App\Models\Live;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentAccessService
{
    public function acquireCourse(
        Request $request,
        Course $course
    ): array {
        return $this->acquire(
            $request,
            'course',
            (int) $course->id,
            (string) $course->title,
            now()->addSeconds(
                max(
                    60,
                    (int) config(
                        'content_access.course_ttl_seconds',
                        120
                    )
                )
            )
        );
    }

    public function acquireLive(
        Request $request,
        Live $live
    ): array {
        $expiresAt =
            $live->end_date_time
            ? $live->end_date_time
                ->copy()
                ->addMinutes(
                    max(
                        0,
                        (int) config(
                            'content_access.live_grace_minutes',
                            2
                        )
                    )
                )
            : now()->addHours(2);

        if (
            $expiresAt->lte(now())
        ) {
            $expiresAt = now()
                ->addMinutes(2);
        }

        return $this->acquire(
            $request,
            'live',
            (int) $live->id,
            (string) $live->title,
            $expiresAt
        );
    }

    public function heartbeatCourse(
        Request $request,
        Course $course
    ): array {
        return $this->acquireCourse(
            $request,
            $course
        );
    }

    public function releaseCourse(
        Request $request,
        Course $course
    ): void {
        $this->release(
            $request,
            'course',
            (int) $course->id
        );
    }

    public function releaseCurrentDevice(
        Request $request
    ): void {
        $user = $request->user();

        if (
            !$user
            || !$user->isStudent()
        ) {
            return;
        }

        $deviceId =
            $this->resolveDeviceId(
                $request
            );

        ContentAccessSession::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'device_id',
                $deviceId
            )
            ->delete();
    }

    public function cleanupExpired(
        int $userId
    ): void {
        ContentAccessSession::query()
            ->where(
                'user_id',
                $userId
            )
            ->where(
                'expires_at',
                '<=',
                now()
            )
            ->delete();
    }

    private function release(
        Request $request,
        string $contentType,
        int $contentId
    ): void {
        $user = $request->user();

        if (
            !$user
            || !$user->isStudent()
        ) {
            return;
        }

        $deviceId =
            $this->resolveDeviceId(
                $request
            );

        ContentAccessSession::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(
                'device_id',
                $deviceId
            )
            ->where(
                'content_type',
                $contentType
            )
            ->where(
                'content_id',
                $contentId
            )
            ->delete();
    }

    private function acquire(
        Request $request,
        string $contentType,
        int $contentId,
        string $contentTitle,
        Carbon $expiresAt
    ): array {
        if (
            !(bool) config(
                'content_access.enabled',
                true
            )
        ) {
            return $this->allowed();
        }

        $user = $request->user();

        if (
            !$user
            || !$user->isStudent()
        ) {
            return $this->allowed();
        }

        $deviceId =
            $this->resolveDeviceId(
                $request
            );

        $deviceLabel =
            $this->deviceLabel(
                (string) $request->userAgent()
            );

        return DB::transaction(
            function () use (
                $request,
                $user,
                $deviceId,
                $deviceLabel,
                $contentType,
                $contentId,
                $contentTitle,
                $expiresAt
            ) {
                DB::table('users')
                    ->where(
                        'id',
                        $user->id
                    )
                    ->lockForUpdate()
                    ->first();

                $this->cleanupExpired(
                    (int) $user->id
                );

                $otherDevice =
                    ContentAccessSession::query()
                        ->where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'device_id',
                            '!=',
                            $deviceId
                        )
                        ->where(
                            'expires_at',
                            '>',
                            now()
                        )
                        ->orderByDesc(
                            'last_seen_at'
                        )
                        ->first();

                if ($otherDevice) {
                    return $this->denied(
                        $otherDevice
                    );
                }

                $session =
                    ContentAccessSession::query()
                        ->firstOrNew([
                            'user_id' =>
                                $user->id,
                            'device_id' =>
                                $deviceId,
                            'content_type' =>
                                $contentType,
                            'content_id' =>
                                $contentId,
                        ]);

                $session->device_label =
                    $deviceLabel;

                $session->content_title =
                    mb_substr(
                        $contentTitle,
                        0,
                        255
                    );

                $session->last_seen_at =
                    now();

                $session->expires_at =
                    $expiresAt;

                $session->ip_address =
                    $request->ip();

                $session->user_agent =
                    mb_substr(
                        (string) $request
                            ->userAgent(),
                        0,
                        2000
                    );

                $session->save();

                return $this->allowed();
            },
            3
        );
    }

    private function resolveDeviceId(
        Request $request
    ): string {
        $cookieName =
            (string) config(
                'content_access.device_cookie',
                'ssa_device_id'
            );

        $deviceId = trim(
            (string) $request->cookie(
                $cookieName
            )
        );

        if ($deviceId !== '') {
            return $deviceId;
        }

        $deviceId = trim(
            (string) $request
                ->session()
                ->get(
                    $cookieName,
                    ''
                )
        );

        if ($deviceId === '') {
            $deviceId =
                (string) Str::uuid();

            $request
                ->session()
                ->put(
                    $cookieName,
                    $deviceId
                );
        }

        Cookie::queue(
            cookie(
                $cookieName,
                $deviceId,
                max(
                    1,
                    (int) config(
                        'content_access.device_cookie_days',
                        730
                    )
                ) * 24 * 60,
                '/',
                null,
                config(
                    'session.secure'
                ),
                true,
                false,
                'Lax'
            )
        );

        return $deviceId;
    }

    private function deviceLabel(
        string $userAgent
    ): string {
        $ua = mb_strtolower(
            $userAgent
        );

        $os = 'Appareil';

        if (
            strpos(
                $ua,
                'iphone'
            ) !== false
        ) {
            $os = 'iPhone';
        } elseif (
            strpos(
                $ua,
                'ipad'
            ) !== false
        ) {
            $os = 'iPad';
        } elseif (
            strpos(
                $ua,
                'android'
            ) !== false
        ) {
            $os = 'Android';
        } elseif (
            strpos(
                $ua,
                'windows'
            ) !== false
        ) {
            $os = 'Windows';
        } elseif (
            strpos(
                $ua,
                'macintosh'
            ) !== false
            || strpos(
                $ua,
                'mac os'
            ) !== false
        ) {
            $os = 'Mac';
        } elseif (
            strpos(
                $ua,
                'linux'
            ) !== false
        ) {
            $os = 'Linux';
        }

        $browser = 'Navigateur';

        if (
            strpos(
                $ua,
                'edg/'
            ) !== false
        ) {
            $browser = 'Edge';
        } elseif (
            strpos(
                $ua,
                'opr/'
            ) !== false
            || strpos(
                $ua,
                'opera'
            ) !== false
        ) {
            $browser = 'Opera';
        } elseif (
            strpos(
                $ua,
                'firefox/'
            ) !== false
        ) {
            $browser = 'Firefox';
        } elseif (
            strpos(
                $ua,
                'chrome/'
            ) !== false
            || strpos(
                $ua,
                'crios/'
            ) !== false
        ) {
            $browser = 'Chrome';
        } elseif (
            strpos(
                $ua,
                'safari/'
            ) !== false
        ) {
            $browser = 'Safari';
        }

        return $os
            . ' · '
            . $browser;
    }

    private function allowed(): array
    {
        return [
            'allowed' => true,
            'code' => 'allowed',
            'message' => 'Accès autorisé.',
            'device_label' => null,
            'content_title' => null,
        ];
    }

    private function denied(
        ContentAccessSession $session
    ): array {
        $device =
            $session->device_label
            ?: 'un autre appareil';

        $content =
            trim(
                (string) $session
                    ->content_title
            );

        $message =
            'Ce compte utilise déjà un contenu protégé '
            . 'sur un autre appareil ('
            . $device
            . ').';

        if ($content !== '') {
            $message .=
                ' Contenu actif : '
                . $content
                . '.';
        }

        $message .=
            ' Un seul appareil peut regarder un Live '
            . 'ou un cours à la fois.';

        return [
            'allowed' => false,
            'code' =>
                'other_device_active',
            'message' =>
                $message,
            'device_label' =>
                $device,
            'content_title' =>
                $content !== ''
                    ? $content
                    : null,
        ];
    }
}
