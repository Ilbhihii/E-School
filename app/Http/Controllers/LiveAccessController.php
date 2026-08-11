<?php

namespace App\Http\Controllers;

use App\Models\Live;
use App\Services\LiveAccessService;
use App\Services\ContentAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class LiveAccessController extends Controller
{
    public function requestAccess(
        Request $request,
        Live $live,
        LiveAccessService $accessService,
        ContentAccessService $contentAccess
    ): RedirectResponse {
        $decision = $accessService->evaluate(
            $request->user(),
            $live
        );

        if (!$decision['allowed']) {
            $accessService->record(
                $request,
                $live,
                $decision
            );

            return $this->deny($decision);
        }

        if (
            $request->user()
            && $request->user()->isStudent()
        ) {
            $contentDecision =
                $contentAccess->acquireLive(
                    $request,
                    $live
                );

            if (
                !$contentDecision['allowed']
            ) {
                $accessService->record(
                    $request,
                    $live,
                    $contentDecision
                );

                return $this->deny(
                    $contentDecision
                );
            }
        }

        $signedUrl = URL::temporarySignedRoute(
            'live.join.signed',
            now()->addMinutes(
                max(
                    1,
                    (int) config(
                        'live.signed_link_minutes',
                        3
                    )
                )
            ),
            ['live' => $live->id]
        );

        return redirect()->to($signedUrl);
    }

    public function join(
        Request $request,
        Live $live,
        LiveAccessService $accessService,
        ContentAccessService $contentAccess
    ): RedirectResponse {
        $decision = $accessService->evaluate(
            $request->user(),
            $live
        );

        if (
            $decision['allowed']
            && $request->user()
            && $request->user()->isStudent()
        ) {
            $contentDecision =
                $contentAccess->acquireLive(
                    $request,
                    $live
                );

            if (
                !$contentDecision['allowed']
            ) {
                $decision =
                    $contentDecision;
            }
        }

        $accessService->record(
            $request,
            $live,
            $decision
        );

        if (!$decision['allowed']) {
            return $this->deny($decision);
        }

        return redirect()->away($live->stream_url);
    }

    private function deny(array $decision): RedirectResponse
    {
        $route = 'front.lives';

        if (
            auth()->check()
            && auth()->user()->isStudent()
        ) {
            $route = match ($decision['code']) {
                'account_inactive' => 'student.waiting',
                default => 'student.lives',
            };
        }

        return redirect()
            ->route($route)
            ->with('error', $decision['message']);
    }
}
