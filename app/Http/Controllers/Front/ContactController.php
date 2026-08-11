<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ContactRequestMailable;
use App\Services\ContactLeadService;
use App\Services\ContactSpreadsheetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    protected $leadService;
    protected $spreadsheetSync;

    public function __construct(
        ContactLeadService $leadService,
        ContactSpreadsheetSyncService $spreadsheetSync
    ) {
        $this->leadService = $leadService;
        $this->spreadsheetSync =
            $spreadsheetSync;
    }

    /**
     * Prise de contact publique :
     * 1. validation,
     * 2. BDD sans doublon,
     * 3. historique du remplissage,
     * 4. e-mail,
     * 5. tableau en ligne.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'email' => [
                'required',
                'email:rfc',
                'max:190',
            ],
            'phone' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9+().\s-]{6,30}$/',
            ],
            'reason' => [
                'required',
                'string',
                'max:1500',
            ],
            'marketing_consent' => [
                'nullable',
                'boolean',
            ],
            /*
             * Honeypot anti-robot.
             */
            'website' => [
                'nullable',
                'string',
                'max:0',
            ],
        ], [
            'first_name.required' =>
                'Le prénom est obligatoire.',
            'last_name.required' =>
                'Le nom est obligatoire.',
            'email.required' =>
                'L’adresse e-mail est obligatoire.',
            'email.email' =>
                'Veuillez saisir une adresse e-mail valide.',
            'phone.required' =>
                'Le numéro de téléphone est obligatoire.',
            'phone.regex' =>
                'Veuillez saisir un numéro de téléphone valide.',
            'reason.required' =>
                'Le commentaire ou la raison est obligatoire.',
            'reason.max' =>
                'Le commentaire ne doit pas dépasser 1500 caractères.',
            'website.max' =>
                'La demande n’a pas pu être envoyée.',
        ]);

        $validated['marketing_consent'] =
            $request->boolean(
                'marketing_consent'
            );

        /*
         * La BDD devient la source principale.
         * Même si Gmail ou le tableau en ligne rencontre un problème,
         * la demande n'est pas perdue.
         */
        try {
            $lead = $this->leadService
                ->register(
                    $validated,
                    $request
                );
        } catch (Throwable $exception) {
            Log::error(
                'Échec de l’enregistrement '
                . 'de la prise de contact.',
                [
                    'email' =>
                        $validated['email'],
                    'exception' =>
                        $exception->getMessage(),
                ]
            );

            return redirect(
                route('home')
                . '#prise-de-contact'
            )
                ->withInput(
                    $request->except(
                        'website'
                    )
                )
                ->with(
                    'contact_error',
                    'Votre demande n’a pas pu être '
                    . 'enregistrée pour le moment. '
                    . 'Veuillez réessayer.'
                );
        }

        $mailPayload = [
            'first_name' =>
                $validated['first_name'],
            'last_name' =>
                $validated['last_name'],
            'email' =>
                $validated['email'],
            'phone' =>
                $validated['phone'],
            'reason' =>
                $validated['reason'],
            'marketing_consent' =>
                $validated[
                    'marketing_consent'
                ],
            'submissions_count' =>
                $lead->submissions_count,
            'is_repeat' =>
                $lead->submissions_count > 1,
        ];

        try {
            Mail::to(
                config(
                    'contact.recipient',
                    'contact.smartschoolacademy@gmail.com'
                )
            )->send(
                new ContactRequestMailable(
                    $mailPayload
                )
            );
        } catch (Throwable $exception) {
            /*
             * Le contact est déjà sauvegardé.
             * On journalise l'erreur sans perdre le prospect.
             */
            Log::error(
                'Échec de l’envoi e-mail '
                . 'de la prise de contact.',
                [
                    'contact_id' =>
                        $lead->id,
                    'email' =>
                        $lead->email,
                    'exception' =>
                        $exception->getMessage(),
                ]
            );
        }

        /*
         * La synchronisation externe ne bloque jamais
         * l'enregistrement du contact.
         */
        $this->spreadsheetSync
            ->sync($lead);

        return redirect(
            route('home')
            . '#prise-de-contact'
        )->with(
            'contact_success',
            'Merci ! Votre demande a bien été '
            . 'enregistrée. Notre équipe vous '
            . 'contactera rapidement.'
        );
    }
}
