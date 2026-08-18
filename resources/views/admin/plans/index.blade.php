@extends('layouts.admin')

@section('title', 'Offres & abonnements')
@section('page_title', 'Offres & abonnements')
@section('breadcrumb', 'Gestion des offres')

@section('content')
<div class="admin-plans-page">
    <section class="plan-admin-hero">
        <div>
            <span class="plan-kicker"><i class="bi bi-tags-fill"></i> Catalogue commercial</span>
            <h2>Gérez les offres du site</h2>
            <p>
                Ajoutez, modifiez, masquez ou supprimez une offre. Les offres actives
                sont utilisées automatiquement sur la page publique <strong>/plans</strong>
                et sur la page de paiement.
            </p>
        </div>
        <div class="plan-hero-actions">
            <a href="{{ route('plans') }}" target="_blank" class="plan-btn plan-btn-soft">
                <i class="bi bi-box-arrow-up-right"></i> Voir le site
            </a>
            <a href="{{ route('admin.plans.create') }}" class="plan-btn plan-btn-primary">
                <i class="bi bi-plus-lg"></i> Nouvelle offre
            </a>
        </div>
    </section>

    <section class="plan-admin-stats">
        <article><span><i class="bi bi-collection-fill"></i></span><div><small>Total</small><strong>{{ $stats['total'] }}</strong></div></article>
        <article class="is-green"><span><i class="bi bi-eye-fill"></i></span><div><small>Actives</small><strong>{{ $stats['active'] }}</strong></div></article>
        <article class="is-orange"><span><i class="bi bi-eye-slash-fill"></i></span><div><small>Masquées</small><strong>{{ $stats['inactive'] }}</strong></div></article>
        <article class="is-violet"><span><i class="bi bi-stars"></i></span><div><small>Recommandée</small><strong>{{ $stats['recommended'] }}</strong></div></article>
    </section>

    <section class="plan-admin-panel">
        <div class="plan-panel-head">
            <div>
                <span>Catalogue</span>
                <h3>Offres configurées</h3>
            </div>
            <small>Le code interne ne peut plus être modifié après création.</small>
        </div>

        @if($plans->isEmpty())
            <div class="plan-empty">
                <i class="bi bi-tags"></i>
                <h3>Aucune offre</h3>
                <p>Créez votre première formule pour l'afficher sur /plans.</p>
                <a href="{{ route('admin.plans.create') }}" class="plan-btn plan-btn-primary">Créer une offre</a>
            </div>
        @else
            <div class="plan-table-wrap">
                <table class="plan-admin-table">
                    <thead>
                        <tr>
                            <th>Offre</th>
                            <th>Prix</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Ordre</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td>
                                    <div class="plan-name-cell">
                                        <span class="plan-mini-icon"><i class="bi {{ $plan->icon ?: 'bi-stars' }}"></i></span>
                                        <div>
                                            <div class="plan-name-line">
                                                <strong>{{ $plan->name }}</strong>
                                                @if($plan->is_recommended)
                                                    <span class="plan-chip recommended"><i class="bi bi-stars"></i> Recommandée</span>
                                                @endif
                                                @if($plan->isSystemPlan())
                                                    <span class="plan-chip system">Système</span>
                                                @endif
                                            </div>
                                            <small>{{ $plan->scope ?: 'Aucun périmètre' }}</small>
                                            <code>{{ $plan->code }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="plan-price-cell">
                                        <strong>{{ $plan->amount_display }} {{ $plan->currency_symbol }}</strong>
                                        <span>/ {{ $plan->period }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="plan-methods">
                                        @if($plan->allow_paypal)<span title="PayPal"><i class="bi bi-paypal"></i> PayPal</span>@endif
                                        @if($plan->allow_bank)<span title="Virement"><i class="bi bi-bank"></i> Virement</span>@endif
                                        @if(!$plan->allow_paypal && !$plan->allow_bank)<em>Aucun</em>@endif
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.plans.status', $plan) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="plan-status {{ $plan->is_active ? 'active' : 'inactive' }}">
                                            <i class="bi {{ $plan->is_active ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }}"></i>
                                            {{ $plan->is_active ? 'Active' : 'Masquée' }}
                                        </button>
                                    </form>
                                </td>
                                <td><span class="plan-order">{{ $plan->sort_order }}</span></td>
                                <td>
                                    <div class="plan-row-actions">
                                        @if($plan->is_active)
                                            <a href="{{ route('plans', ['offer' => $plan->code]) }}" target="_blank" title="Prévisualiser" class="plan-icon-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.plans.edit', $plan) }}" title="Modifier" class="plan-icon-btn edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Retirer cette offre ? Une offre déjà utilisée sera seulement désactivée.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer / désactiver" class="plan-icon-btn danger">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<style>
.admin-plans-page{display:flex;flex-direction:column;gap:18px;color:#e5edf8}.plan-admin-hero{display:flex;align-items:center;justify-content:space-between;gap:24px;padding:24px;border:1px solid rgba(148,163,184,.13);border-radius:22px;background:linear-gradient(145deg,rgba(19,31,52,.96),rgba(10,19,34,.96));box-shadow:0 18px 40px rgba(0,0,0,.18)}.plan-kicker{display:inline-flex;align-items:center;gap:7px;margin-bottom:7px;color:#8fb0ff;font-size:.67rem;font-weight:800;letter-spacing:.11em;text-transform:uppercase}.plan-admin-hero h2{margin:0 0 6px;color:#fff;font-family:Poppins,sans-serif;font-size:1.55rem;font-weight:800}.plan-admin-hero p{max-width:760px;margin:0;color:#8696ad;font-size:.78rem;line-height:1.65}.plan-admin-hero p strong{color:#c9d6e9}.plan-hero-actions{display:flex;gap:9px;flex-wrap:wrap}.plan-btn{min-height:42px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:0 14px;border:1px solid rgba(148,163,184,.14);border-radius:11px;font-size:.72rem;font-weight:800;text-decoration:none}.plan-btn-soft{color:#c4d0e2;background:rgba(255,255,255,.04)}.plan-btn-primary{color:#fff;border-color:rgba(79,114,245,.32);background:linear-gradient(135deg,#4367ef,#7354e8);box-shadow:0 10px 24px rgba(79,114,245,.18)}.plan-admin-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.plan-admin-stats article{display:flex;align-items:center;gap:12px;padding:16px;border:1px solid rgba(148,163,184,.11);border-radius:16px;background:#0d1728}.plan-admin-stats article>span{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;color:#8fb0ff;background:rgba(79,114,245,.11)}.plan-admin-stats small{display:block;color:#65758d;font-size:.62rem}.plan-admin-stats strong{display:block;margin-top:2px;color:#fff;font-size:1.2rem}.plan-admin-stats .is-green>span{color:#55d9a8;background:rgba(36,183,134,.1)}.plan-admin-stats .is-orange>span{color:#f0b856;background:rgba(225,165,58,.1)}.plan-admin-stats .is-violet>span{color:#b39bff;background:rgba(117,84,232,.1)}.plan-admin-panel{overflow:hidden;border:1px solid rgba(148,163,184,.12);border-radius:20px;background:#0b1525}.plan-panel-head{display:flex;align-items:flex-end;justify-content:space-between;gap:15px;padding:18px 20px;border-bottom:1px solid rgba(148,163,184,.09)}.plan-panel-head span{color:#8292aa;font-size:.62rem;text-transform:uppercase;letter-spacing:.1em}.plan-panel-head h3{margin:3px 0 0;color:#fff;font-size:1rem;font-weight:800}.plan-panel-head>small{color:#5f7189;font-size:.61rem}.plan-table-wrap{overflow-x:auto}.plan-admin-table{width:100%;min-width:900px;border-collapse:collapse}.plan-admin-table th{padding:11px 16px;color:#64748b;background:rgba(255,255,255,.018);font-size:.58rem;text-transform:uppercase;letter-spacing:.08em}.plan-admin-table td{padding:14px 16px;border-top:1px solid rgba(148,163,184,.07);vertical-align:middle}.plan-admin-table tbody tr:hover{background:rgba(79,114,245,.025)}.plan-name-cell{display:flex;align-items:center;gap:11px}.plan-mini-icon{width:40px;height:40px;display:grid;place-items:center;flex:0 0 40px;border-radius:12px;color:#a9b9ff;background:linear-gradient(135deg,rgba(79,114,245,.17),rgba(117,84,232,.12));border:1px solid rgba(79,114,245,.16)}.plan-name-line{display:flex;align-items:center;gap:6px;flex-wrap:wrap}.plan-name-line strong{color:#eaf0f8;font-size:.74rem}.plan-name-cell small{display:block;margin:2px 0;color:#718198;font-size:.58rem}.plan-name-cell code{color:#8194b1;font-size:.54rem}.plan-chip{display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:999px;font-size:.49rem;font-weight:800}.plan-chip.recommended{color:#c7b7ff;background:rgba(117,84,232,.11)}.plan-chip.system{color:#8cc9ff;background:rgba(35,168,202,.09)}.plan-price-cell strong{display:block;color:#fff;font-size:.77rem}.plan-price-cell span{color:#65758c;font-size:.56rem}.plan-methods{display:flex;gap:5px;flex-wrap:wrap}.plan-methods span{display:inline-flex;align-items:center;gap:4px;padding:4px 7px;border-radius:7px;color:#9fb3cb;background:rgba(255,255,255,.035);font-size:.53rem}.plan-methods em{color:#687990;font-size:.55rem}.plan-status{display:inline-flex;align-items:center;gap:5px;padding:5px 8px;border:1px solid transparent;border-radius:8px;background:none;font-size:.55rem;font-weight:800;cursor:pointer}.plan-status.active{color:#59dca9;border-color:rgba(36,183,134,.15);background:rgba(36,183,134,.07)}.plan-status.inactive{color:#f1b95b;border-color:rgba(225,165,58,.15);background:rgba(225,165,58,.07)}.plan-order{display:inline-grid;min-width:28px;height:28px;place-items:center;border-radius:8px;color:#96a6bd;background:rgba(255,255,255,.035);font-size:.57rem}.plan-row-actions{display:flex;justify-content:flex-end;gap:5px}.plan-row-actions form{margin:0}.plan-icon-btn{width:32px;height:32px;display:grid;place-items:center;border:1px solid rgba(148,163,184,.12);border-radius:8px;color:#8ba0bd;background:rgba(255,255,255,.025);text-decoration:none;cursor:pointer}.plan-icon-btn.edit{color:#aebaff}.plan-icon-btn.danger{color:#f0959e}.plan-empty{padding:50px 20px;text-align:center}.plan-empty>i{font-size:2rem;color:#52647f}.plan-empty h3{margin:10px 0 5px;color:#fff}.plan-empty p{color:#6f8199;font-size:.72rem}.plan-empty .plan-btn{margin-top:10px}@media(max-width:900px){.plan-admin-hero{align-items:flex-start;flex-direction:column}.plan-admin-stats{grid-template-columns:repeat(2,1fr)}}@media(max-width:520px){.plan-admin-stats{grid-template-columns:1fr}}
</style>
@endsection
