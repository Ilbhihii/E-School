@extends('layouts.admin')

@section('title', 'Contact — ' . $contact->full_name)
@section('page_title', 'Détail du contact')
@section('breadcrumb', 'Communication → Contacts → Détail')

@section('content')
<style>
    .contact-detail-grid {
        display:grid;
        grid-template-columns:minmax(0,1fr) minmax(300px,.45fr);
        gap:16px;
    }
    .contact-box {
        padding:18px;
        border:1px solid rgba(148,163,184,.12);
        border-radius:14px;
        background:rgba(15,23,42,.55);
    }
    .contact-kv {
        display:grid;
        grid-template-columns:150px 1fr;
        gap:10px;
        padding:9px 0;
        border-bottom:1px solid rgba(148,163,184,.08);
    }
    .contact-kv:last-child { border-bottom:0; }
    .contact-kv span { color:var(--adm-text-muted); font-size:.72rem; }
    .contact-kv strong { color:var(--adm-text); font-size:.8rem; }
    .request-item {
        padding:14px;
        margin-bottom:10px;
        border:1px solid rgba(148,163,184,.1);
        border-radius:12px;
        background:rgba(2,6,23,.32);
    }
    .request-head {
        display:flex;
        justify-content:space-between;
        gap:10px;
        margin-bottom:8px;
    }
    .request-reason {
        white-space:pre-line;
        color:#cbd5e1;
        font-size:.78rem;
        line-height:1.6;
    }
    @media(max-width:900px) {
        .contact-detail-grid { grid-template-columns:1fr; }
    }
</style>

<div class="adm-page-header">
    <div>
        <h1>
            <i class="bi bi-person-vcard" style="color:#818CF8;"></i>
            {{ $contact->full_name }}
        </h1>
        <div class="subtitle">
            {{ $contact->submissions_count }} remplissage(s) du formulaire.
        </div>
    </div>

    <a
        href="{{ route('admin.contacts.index') }}"
        class="adm-btn adm-btn-ghost"
    >
        <i class="bi bi-arrow-left"></i>
        Retour
    </a>
</div>

<div class="contact-detail-grid">
    <div>
        <div class="contact-box">
            <h3 style="margin:0 0 10px;color:var(--adm-text);font-size:1rem;">
                Historique des demandes
            </h3>

            @forelse($contact->requests as $requestItem)
                <div class="request-item">
                    <div class="request-head">
                        <strong style="color:var(--adm-text);">
                            Demande #{{ $requestItem->id }}
                        </strong>

                        <small style="color:var(--adm-text-muted);">
                            {{ $requestItem->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <div class="request-reason">
                        {{ $requestItem->reason }}
                    </div>

                    <div style="margin-top:9px;color:var(--adm-text-muted);font-size:.68rem;">
                        E-mail utilisé : {{ $requestItem->email }}
                        · Téléphone : {{ $requestItem->phone }}
                        · Pays : {{ $requestItem->country ?: '—' }}
                        · Mailing :
                        {{ $requestItem->marketing_consent ? 'Oui' : 'Non' }}
                    </div>
                </div>
            @empty
                <p style="color:var(--adm-text-muted);">
                    Aucun historique.
                </p>
            @endforelse
        </div>
    </div>

    <aside class="contact-box">
        <h3 style="margin:0 0 10px;color:var(--adm-text);font-size:1rem;">
            Fiche prospect
        </h3>

        <div class="contact-kv">
            <span>Nom complet</span>
            <strong>{{ $contact->full_name }}</strong>
        </div>

        <div class="contact-kv">
            <span>E-mail</span>
            <strong>
                <a
                    href="mailto:{{ $contact->email }}"
                    style="color:#93c5fd;"
                >
                    {{ $contact->email }}
                </a>
            </strong>
        </div>

        <div class="contact-kv">
            <span>Téléphone</span>
            <strong>
                <a
                    href="tel:{{ $contact->phone }}"
                    style="color:#93c5fd;"
                >
                    {{ $contact->phone }}
                </a>
            </strong>
        </div>

        <div class="contact-kv">
            <span>Pays</span>
            <strong>{{ $contact->country ?: '—' }}</strong>
        </div>

        <div class="contact-kv">
            <span>Remplissages</span>
            <strong>{{ $contact->submissions_count }}</strong>
        </div>

        <div class="contact-kv">
            <span>Première demande</span>
            <strong>
                {{
                    optional($contact->first_contact_at)
                        ->format('d/m/Y H:i')
                    ?? '—'
                }}
            </strong>
        </div>

        <div class="contact-kv">
            <span>Dernière demande</span>
            <strong>
                {{
                    optional($contact->last_contact_at)
                        ->format('d/m/Y H:i')
                    ?? '—'
                }}
            </strong>
        </div>

        <div class="contact-kv">
            <span>Mailing</span>
            <strong>
                {{ $contact->marketing_consent ? 'Autorisé' : 'Non autorisé' }}
            </strong>
        </div>

        <div class="contact-kv">
            <span>Tableau en ligne</span>
            <strong>
                {{
                    $contact->sheet_synced_at
                        ? 'Synchronisé le '
                            . $contact->sheet_synced_at->format('d/m/Y H:i')
                        : 'Pas encore synchronisé'
                }}
            </strong>
        </div>
    </aside>
</div>
@endsection
