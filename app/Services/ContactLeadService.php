<?php

namespace App\Services;

use App\Models\ContactLead;
use App\Models\ContactRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactLeadService
{
    /**
     * Enregistre la demande sans dupliquer le contact principal.
     *
     * Doublon = même e-mail normalisé OU même téléphone normalisé.
     * Chaque remplissage reste toutefois conservé dans contact_requests.
     */
    public function register(array $data, Request $request)
    {
        $emailNormalized =
            $this->normalizeEmail($data['email']);

        $phoneNormalized =
            $this->normalizePhone(
                $data['phone'],
                $data['country'] ?? null
            );

        try {
            return $this->persist(
                $data,
                $request,
                $emailNormalized,
                $phoneNormalized
            );
        } catch (QueryException $exception) {
            /*
             * Si deux demandes identiques arrivent presque exactement
             * au même instant, les index uniques peuvent déclencher
             * une collision. On refait alors la recherche puis la mise
             * à jour afin d'éviter de créer un doublon.
             */
            if (!$this->isDuplicateKeyException($exception)) {
                throw $exception;
            }

            return $this->persist(
                $data,
                $request,
                $emailNormalized,
                $phoneNormalized
            );
        }
    }

    protected function persist(
        array $data,
        Request $request,
        $emailNormalized,
        $phoneNormalized
    ) {
        return DB::transaction(function () use (
            $data,
            $request,
            $emailNormalized,
            $phoneNormalized
        ) {
            /*
             * Priorité à l'e-mail, puis au téléphone.
             * Cela rend le rapprochement déterministe si des données
             * anciennes contiennent une incohérence.
             */
            $lead = ContactLead::query()
                ->where(
                    'email_normalized',
                    $emailNormalized
                )
                ->lockForUpdate()
                ->first();

            if (!$lead) {
                $lead = ContactLead::query()
                    ->where(
                        'phone_normalized',
                        $phoneNormalized
                    )
                    ->lockForUpdate()
                    ->first();
            }

            $now = now();

            if ($lead) {
                $lead->first_name = trim(
                    $data['first_name']
                );

                $lead->last_name = trim(
                    $data['last_name']
                );

                $lead->country = trim(
                    $data['country']
                );

                /*
                 * On met à jour l'e-mail ou le téléphone seulement si
                 * la nouvelle valeur n'appartient pas déjà à un autre
                 * contact. Cela évite un conflit d'index unique.
                 */
                $emailUsedByAnother = ContactLead::query()
                    ->where(
                        'email_normalized',
                        $emailNormalized
                    )
                    ->where('id', '<>', $lead->getKey())
                    ->exists();

                if (!$emailUsedByAnother) {
                    $lead->email = trim(
                        $data['email']
                    );

                    $lead->email_normalized =
                        $emailNormalized;
                }

                $phoneUsedByAnother = ContactLead::query()
                    ->where(
                        'phone_normalized',
                        $phoneNormalized
                    )
                    ->where('id', '<>', $lead->getKey())
                    ->exists();

                if (!$phoneUsedByAnother) {
                    $lead->phone = trim(
                        $data['phone']
                    );

                    $lead->phone_normalized =
                        $phoneNormalized;
                }

                $lead->latest_reason = trim(
                    $data['reason']
                );

                $lead->submissions_count =
                    ((int) $lead->submissions_count)
                    + 1;

                $lead->marketing_consent =
                    (bool) $lead->marketing_consent
                    || !empty(
                        $data['marketing_consent']
                    );

                $lead->last_contact_at = $now;
                $lead->save();
            } else {
                $lead = ContactLead::create([
                    'first_name' => trim(
                        $data['first_name']
                    ),
                    'last_name' => trim(
                        $data['last_name']
                    ),
                    'email' => trim(
                        $data['email']
                    ),
                    'email_normalized' =>
                        $emailNormalized,
                    'phone' => trim(
                        $data['phone']
                    ),
                    'phone_normalized' =>
                        $phoneNormalized,
                    'country' => trim(
                        $data['country']
                    ),
                    'latest_reason' => trim(
                        $data['reason']
                    ),
                    'submissions_count' => 1,
                    'marketing_consent' =>
                        !empty(
                            $data['marketing_consent']
                        ),
                    'first_contact_at' => $now,
                    'last_contact_at' => $now,
                ]);
            }

            ContactRequest::create([
                'contact_lead_id' =>
                    $lead->getKey(),
                'first_name' => trim(
                    $data['first_name']
                ),
                'last_name' => trim(
                    $data['last_name']
                ),
                'email' => trim(
                    $data['email']
                ),
                'phone' => trim(
                    $data['phone']
                ),
                'country' => trim(
                    $data['country']
                ),
                'reason' => trim(
                    $data['reason']
                ),
                'marketing_consent' =>
                    !empty(
                        $data['marketing_consent']
                    ),
                'source' => 'homepage',
                'ip_address' =>
                    $request->ip(),
                'user_agent' =>
                    Str::limit(
                        (string) $request->userAgent(),
                        500,
                        ''
                    ),
            ]);

            return $lead->fresh();
        }, 3);
    }

    public function normalizeEmail($email)
    {
        return Str::lower(
            trim((string) $email)
        );
    }

    public function normalizePhone(
        $phone,
        $country = null
    ) {
        $digits = preg_replace(
            '/\D+/',
            '',
            (string) $phone
        );

        if (strpos($digits, '00') === 0) {
            $digits = substr(
                $digits,
                2
            );
        }

        $countryNormalized = Str::lower(
            trim((string) $country)
        );

        $isMorocco = in_array(
            $countryNormalized,
            [
                '',
                'maroc',
                'morocco',
                'المغرب',
            ],
            true
        );

        /*
         * On ne préfixe automatiquement le code +212
         * que pour le Maroc. Pour les autres pays, le visiteur
         * peut saisir son indicatif international.
         */
        if ($isMorocco) {
            $countryCode = preg_replace(
                '/\D+/',
                '',
                (string) config(
                    'contact.default_country_code',
                    '212'
                )
            );

            if (
                $countryCode
                && strlen($digits) === 10
                && strpos($digits, '0') === 0
            ) {
                $digits =
                    $countryCode
                    . substr(
                        $digits,
                        1
                    );
            }
        }

        return $digits;
    }

    protected function isDuplicateKeyException(
        QueryException $exception
    ) {
        $code = (string) $exception->getCode();

        return
            $code === '23000'
            || strpos(
                Str::lower(
                    $exception->getMessage()
                ),
                'duplicate'
            ) !== false;
    }
}
