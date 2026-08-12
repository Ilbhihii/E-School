@extends('layouts.admin')

@section('title', 'Contacts / Prospects — Administration')
@section('page_title', 'Contacts / Prospects')
@section('breadcrumb', 'Communication → Contacts')

@section('content')
<style>
    .lead-stats {
        display:grid;
        grid-template-columns:repeat(4,minmax(0,1fr));
        gap:12px;
        margin-bottom:18px;
    }
    .lead-stat {
        padding:16px;
        border:1px solid rgba(148,163,184,.12);
        border-radius:14px;
        background:rgba(15,23,42,.55);
    }
    .lead-stat span {
        display:block;
        color:var(--adm-text-muted);
        font-size:.72rem;
    }
    .lead-stat strong {
        display:block;
        margin-top:5px;
        color:var(--adm-text);
        font-size:1.5rem;
    }
    .lead-toolbar {
        display:flex;
        gap:10px;
        align-items:end;
        justify-content:space-between;
        flex-wrap:wrap;
        margin-bottom:14px;
    }
    .lead-filters {
        display:flex;
        gap:8px;
        align-items:end;
        flex-wrap:wrap;
    }
    .lead-field label {
        display:block;
        margin-bottom:5px;
        color:var(--adm-text-muted);
        font-size:.68rem;
        font-weight:700;
    }
    .lead-field input,
    .lead-field select {
        min-height:40px;
        padding:0 11px;
        color:var(--adm-text);
        border:1px solid rgba(148,163,184,.16);
        border-radius:10px;
        background:rgba(2,6,23,.45);
        outline:none;
    }
    .lead-search {
        min-width:280px;
    }
    .lead-name {
        display:flex;
        align-items:center;
        gap:9px;
    }
    .lead-avatar {
        display:grid;
        width:34px;
        height:34px;
        place-items:center;
        border-radius:10px;
        background:linear-gradient(135deg,#4f46e5,#7c3aed);
        color:white;
        font-weight:800;
    }
    .lead-name small,
    .lead-reason {
        display:block;
        color:var(--adm-text-muted);
        font-size:.7rem;
    }
    .lead-count {
        display:inline-flex;
        align-items:center;
        gap:5px;
        padding:5px 8px;
        border-radius:999px;
        background:rgba(59,130,246,.12);
        color:#93c5fd;
        font-size:.7rem;
        font-weight:800;
    }
    .lead-count.repeated {
        background:rgba(245,158,11,.14);
        color:#fbbf24;
    }
    .lead-consent {
        display:inline-flex;
        padding:5px 8px;
        border-radius:999px;
        font-size:.68rem;
        font-weight:800;
    }
    .lead-consent.yes {
        background:rgba(34,197,94,.13);
        color:#86efac;
    }
    .lead-consent.no {
        background:rgba(100,116,139,.13);
        color:#cbd5e1;
    }
    @media(max-width:1000px) {
        .lead-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media(max-width:650px) {
        .lead-stats { grid-template-columns:1fr; }
        .lead-search { min-width:100%; width:100%; }
    }
</style>

<div class="adm-page-header">
    <div>
        <h1>
            <i class="bi bi-person-lines-fill" style="color:#818CF8;"></i>
            Contacts / Prospects
        </h1>
        <div class="subtitle">
            Base automatique des personnes ayant rempli le formulaire public.
            Les doublons sont regroupés et le nombre de remplissages est compté.
        </div>
    </div>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a
            href="{{ route('admin.contacts.export') }}"
            class="adm-btn adm-btn-ghost"
        >
            <i class="bi bi-file-earmark-spreadsheet"></i>
            Export Excel / CSV
        </a>

        <a
            href="{{ route('admin.contacts.export', ['mailing' => 1]) }}"
            class="adm-btn adm-btn-primary"
        >
            <i class="bi bi-envelope-check"></i>
            Export mailing autorisé
        </a>
    </div>
</div>

<div class="lead-stats">
    <div class="lead-stat">
        <span>Contacts uniques</span>
        <strong>{{ $stats['contacts'] }}</strong>
    </div>
    <div class="lead-stat">
        <span>Formulaires remplis</span>
        <strong>{{ $stats['requests'] }}</strong>
    </div>
    <div class="lead-stat">
        <span>Contacts récurrents</span>
        <strong>{{ $stats['repeated'] }}</strong>
    </div>
    <div class="lead-stat">
        <span>Mailing autorisé</span>
        <strong>{{ $stats['marketing'] }}</strong>
    </div>
</div>

<div class="adm-card" style="padding:16px;">
    <div class="lead-toolbar">
        <form
            method="GET"
            action="{{ route('admin.contacts.index') }}"
            class="lead-filters"
        >
            <div class="lead-field">
                <label>Recherche</label>
                <input
                    class="lead-search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Nom, e-mail, téléphone, pays, raison..."
                >
            </div>

            <div class="lead-field">
                <label>Doublons / récurrents</label>
                <select name="repeated">
                    <option value="">Tous</option>
                    <option
                        value="1"
                        {{ request('repeated') === '1' ? 'selected' : '' }}
                    >
                        2 remplissages ou plus
                    </option>
                </select>
            </div>

            <div class="lead-field">
                <label>Mailing</label>
                <select name="consent">
                    <option value="">Tous</option>
                    <option value="yes" {{ $consent === 'yes' ? 'selected' : '' }}>
                        Autorisé
                    </option>
                    <option value="no" {{ $consent === 'no' ? 'selected' : '' }}>
                        Non autorisé
                    </option>
                </select>
            </div>

            <button type="submit" class="adm-btn adm-btn-primary">
                <i class="bi bi-search"></i>
                Filtrer
            </button>

            <a
                href="{{ route('admin.contacts.index') }}"
                class="adm-btn adm-btn-ghost"
            >
                Réinitialiser
            </a>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table adm-table">
            <thead>
                <tr>
                    <th>Contact</th>
                    <th>Téléphone</th>
                    <th>Pays</th>
                    <th>Raison récente</th>
                    <th>Remplissages</th>
                    <th>Mailing</th>
                    <th>Dernière demande</th>
                    <th>Tableau</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($contacts as $contact)
                    <tr>
                        <td>
                            <div class="lead-name">
                                <span class="lead-avatar">
                                    {{
                                        mb_strtoupper(
                                            mb_substr(
                                                $contact->first_name,
                                                0,
                                                1
                                            )
                                        )
                                    }}
                                </span>

                                <div>
                                    <strong>{{ $contact->full_name }}</strong>
                                    <small>{{ $contact->email }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <a
                                href="tel:{{ $contact->phone }}"
                                style="color:#93c5fd;text-decoration:none;"
                            >
                                {{ $contact->phone }}
                            </a>
                        </td>

                        <td>
                            {{ $contact->country ?: '—' }}
                        </td>

                        <td style="max-width:300px;">
                            <span
                                class="lead-reason"
                                title="{{ $contact->latest_reason }}"
                            >
                                {{
                                    \Illuminate\Support\Str::limit(
                                        $contact->latest_reason,
                                        90
                                    )
                                }}
                            </span>
                        </td>

                        <td>
                            <span
                                class="lead-count {{
                                    $contact->is_repeated
                                        ? 'repeated'
                                        : ''
                                }}"
                            >
                                <i class="bi bi-arrow-repeat"></i>
                                {{ $contact->submissions_count }}
                            </span>
                        </td>

                        <td>
                            <span
                                class="lead-consent {{
                                    $contact->marketing_consent
                                        ? 'yes'
                                        : 'no'
                                }}"
                            >
                                {{
                                    $contact->marketing_consent
                                        ? 'Oui'
                                        : 'Non'
                                }}
                            </span>
                        </td>

                        <td>
                            {{
                                optional(
                                    $contact->last_contact_at
                                )->format('d/m/Y H:i')
                                ?? '—'
                            }}
                        </td>

                        <td>
                            @if($contact->sheet_synced_at)
                                <span class="lead-consent yes">
                                    <i class="bi bi-check2 me-1"></i>
                                    Synchronisé
                                </span>
                            @else
                                <span class="lead-consent no">
                                    Local
                                </span>
                            @endif
                        </td>

                        <td>
                            <a
                                href="{{
                                    route(
                                        'admin.contacts.show',
                                        $contact
                                    )
                                }}"
                                class="adm-btn adm-btn-ghost adm-btn-sm"
                            >
                                <i class="bi bi-eye"></i>
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:30px;">
                            Aucun contact trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contacts->hasPages())
        <div style="margin-top:16px;">
            {{ $contacts->links() }}
        </div>
    @endif
</div>
@endsection
