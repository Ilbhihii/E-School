<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactLead::query();

        $search = trim(
            (string) $request->query(
                'q',
                ''
            )
        );

        if ($search !== '') {
            $query->where(
                function ($builder) use (
                    $search
                ) {
                    $like =
                        '%' . $search . '%';

                    $builder
                        ->where(
                            'first_name',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'last_name',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'email',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'latest_reason',
                            'like',
                            $like
                        );
                }
            );
        }

        if (
            $request->query(
                'repeated'
            ) === '1'
        ) {
            $query->where(
                'submissions_count',
                '>',
                1
            );
        }

        $consent = $request->query(
            'consent'
        );

        if ($consent === 'yes') {
            $query->where(
                'marketing_consent',
                true
            );
        } elseif ($consent === 'no') {
            $query->where(
                'marketing_consent',
                false
            );
        }

        $contacts = $query
            ->latest('last_contact_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'contacts' =>
                ContactLead::count(),
            'requests' =>
                (int) ContactLead::sum(
                    'submissions_count'
                ),
            'repeated' =>
                ContactLead::where(
                    'submissions_count',
                    '>',
                    1
                )->count(),
            'marketing' =>
                ContactLead::where(
                    'marketing_consent',
                    true
                )->count(),
        ];

        return view(
            'admin.contacts.index',
            compact(
                'contacts',
                'stats',
                'search',
                'consent'
            )
        );
    }

    public function show(ContactLead $contact)
    {
        $contact->load([
            'requests' => function (
                $query
            ) {
                $query->latest();
            },
        ]);

        return view(
            'admin.contacts.show',
            compact('contact')
        );
    }

    public function exportCsv(
        Request $request
    ) {
        $mailingOnly =
            $request->boolean(
                'mailing'
            );

        $query = ContactLead::query()
            ->orderBy(
                'last_name'
            )
            ->orderBy(
                'first_name'
            );

        if ($mailingOnly) {
            $query->where(
                'marketing_consent',
                true
            );
        }

        $fileName =
            $mailingOnly
                ? 'contacts-mailing-'
                    . now()->format(
                        'Y-m-d'
                    )
                    . '.csv'
                : 'contacts-'
                    . now()->format(
                        'Y-m-d'
                    )
                    . '.csv';

        return new StreamedResponse(
            function () use ($query) {
                $handle = fopen(
                    'php://output',
                    'w'
                );

                /*
                 * BOM UTF-8 pour un affichage correct
                 * des accents dans Excel.
                 */
                fwrite(
                    $handle,
                    "\xEF\xBB\xBF"
                );

                fputcsv(
                    $handle,
                    [
                        'ID',
                        'Nom',
                        'Prénom',
                        'E-mail',
                        'Téléphone',
                        'Raison récente',
                        'Nombre de remplissages',
                        'Première demande',
                        'Dernière demande',
                        'Consentement mailing',
                    ],
                    ';'
                );

                foreach (
                    $query->cursor()
                    as $contact
                ) {
                    fputcsv(
                        $handle,
                        [
                            $contact->id,
                            $contact->last_name,
                            $contact->first_name,
                            $contact->email,
                            $contact->phone,
                            $contact->latest_reason,
                            $contact
                                ->submissions_count,
                            optional(
                                $contact
                                    ->first_contact_at
                            )->format(
                                'd/m/Y H:i'
                            ),
                            optional(
                                $contact
                                    ->last_contact_at
                            )->format(
                                'd/m/Y H:i'
                            ),
                            $contact
                                ->marketing_consent
                                ? 'Oui'
                                : 'Non',
                        ],
                        ';'
                    );
                }

                fclose($handle);
            },
            200,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
                'Content-Disposition' =>
                    'attachment; filename="'
                    . $fileName
                    . '"',
            ]
        );
    }
}
