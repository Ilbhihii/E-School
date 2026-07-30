<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title>
        Rapport du test {{ $submission->id }}
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #172033;
            font-size: 12px;
            line-height: 1.5;
        }

        .header {
            padding-bottom: 18px;
            border-bottom: 2px solid #2563EB;
        }

        .header h1 {
            margin: 0 0 6px;
            color: #0F172A;
            font-size: 22px;
        }

        .header p {
            margin: 0;
            color: #64748B;
        }

        .status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 9px;
            border-radius: 12px;
            color: #ffffff;
            background: #2563EB;
            font-weight: bold;
        }

        .section {
            margin-top: 20px;
        }

        .section h2 {
            margin: 0 0 9px;
            padding-bottom: 5px;
            border-bottom: 1px solid #CBD5E1;
            color: #1E293B;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 7px 8px;
            border: 1px solid #E2E8F0;
            vertical-align: top;
        }

        td:first-child {
            width: 34%;
            color: #64748B;
            background: #F8FAFC;
        }

        .score {
            color: #2563EB;
            font-size: 22px;
            font-weight: bold;
        }

        .comment {
            padding: 12px;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            background: #F8FAFC;
            white-space: pre-wrap;
        }

        .annotation {
            margin-bottom: 8px;
            padding: 9px;
            border-left: 3px solid #2563EB;
            background: #F8FAFC;
        }

        .annotation strong {
            display: block;
            margin-bottom: 3px;
        }

        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #CBD5E1;
            color: #94A3B8;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rapport de correction</h1>

        <p>
            Smart School Academy · Test écrit Soutien Lycée
        </p>

        <span class="status">
            {{ $submission->statusLabel() }}
        </span>
    </div>

    <div class="section">
        <h2>Étudiant et parcours</h2>

        <table>
            <tr>
                <td>Étudiant</td>
                <td>{{ $submission->user?->name ?? '—' }}</td>
            </tr>

            <tr>
                <td>Email</td>
                <td>{{ $submission->user?->email ?? '—' }}</td>
            </tr>

            <tr>
                <td>Test</td>
                <td>{{ $submission->test_title }}</td>
            </tr>

            <tr>
                <td>Matière</td>
                <td>{{ $submission->subject?->name ?? '—' }}</td>
            </tr>

            <tr>
                <td>Niveau</td>
                <td>{{ $submission->level?->name ?? '—' }}</td>
            </tr>

            <tr>
                <td>Matière du BAC</td>
                <td>{{ $submission->classRoom?->name ?? '—' }}</td>
            </tr>

            <tr>
                <td>Date d’envoi</td>
                <td>
                    {{
                        optional(
                            $submission->submitted_at
                        )->format('d/m/Y à H:i')
                        ?? '—'
                    }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Résultat</h2>

        <table>
            <tr>
                <td>Note</td>
                <td>
                    <span class="score">
                        {{
                            $submission->score !== null
                                ? $submission->score . '/20'
                                : 'Non noté'
                        }}
                    </span>
                </td>
            </tr>

            <tr>
                <td>Correcteur</td>
                <td>
                    {{ $submission->reviewer?->name ?? '—' }}
                </td>
            </tr>

            <tr>
                <td>Date de correction</td>
                <td>
                    {{
                        optional(
                            $submission->reviewed_at
                        )->format('d/m/Y à H:i')
                        ?? '—'
                    }}
                </td>
            </tr>

            <tr>
                <td>Accès aux cours</td>
                <td>
                    {{
                        $submission->isApproved()
                            ? 'Autorisé'
                            : 'Non autorisé'
                    }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Commentaire général</h2>

        <div class="comment">
            {{
                $submission->teacher_comment
                ?: 'Aucun commentaire.'
            }}
        </div>
    </div>

    <div class="section">
        <h2>Annotations des feuilles</h2>

        @forelse(
            $submission->annotations()
            as $imageIndex => $annotation
        )
            @if(trim((string) $annotation) !== '')
                <div class="annotation">
                    <strong>
                        Feuille {{ $imageIndex + 1 }}
                    </strong>

                    {{ $annotation }}
                </div>
            @endif
        @empty
            <p>Aucune annotation spécifique.</p>
        @endforelse
    </div>

    <div class="footer">
        Rapport généré le
        {{ now()->format('d/m/Y à H:i') }}
        · Identifiant du test :
        {{ $submission->id }}
    </div>
</body>
</html>
