@extends('layouts.admin')

@section('title', 'Paiements étudiants')
@section('page_title', 'Paiements étudiants')
@section('breadcrumb', 'Suivi des paiements 4 mois et année')

@push('styles')
<style>
.pay-page{display:flex;flex-direction:column;gap:22px}.pay-hero{display:flex;justify-content:space-between;gap:22px;padding:28px;border:1px solid var(--ap-border);border-radius:22px;background:radial-gradient(circle at 90% 10%,rgba(37,99,235,.2),transparent 20rem),linear-gradient(135deg,rgba(18,30,51,.98),rgba(10,18,32,.95));box-shadow:var(--ap-shadow-soft)}.pay-hero h2{margin:0 0 8px;color:#fff;font-size:1.7rem;font-weight:800}.pay-hero p{margin:0;color:var(--ap-muted);max-width:690px}.pay-actions{display:flex;gap:10px;align-items:flex-start;flex-wrap:wrap}.pay-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:42px;padding:0 15px;border:1px solid var(--ap-border);border-radius:11px;background:rgba(255,255,255,.04);color:#dbeafe;text-decoration:none;font-weight:750;font-size:.78rem}.pay-btn.primary{border-color:rgba(96,165,250,.25);background:linear-gradient(135deg,#2563eb,#6d28d9);color:#fff}.pay-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:13px}.pay-stat{padding:17px;border:1px solid var(--ap-border);border-radius:17px;background:rgba(17,27,46,.82)}.pay-stat span{display:block;color:var(--ap-muted);font-size:.7rem}.pay-stat strong{display:block;margin-top:7px;color:#fff;font-size:1.45rem}.pay-panel{overflow:hidden;border:1px solid var(--ap-border);border-radius:22px;background:rgba(12,21,37,.94);box-shadow:var(--ap-shadow)}.pay-filters{display:grid;grid-template-columns:minmax(220px,1fr) 180px 180px auto;gap:10px;padding:18px;border-bottom:1px solid var(--ap-border)}.pay-control{height:43px;width:100%;border:1px solid var(--ap-border);border-radius:10px;background:#0a1220;color:#e2e8f0;padding:0 12px;outline:none}.pay-filter-btn{height:43px;border:1px solid var(--ap-border);border-radius:10px;background:rgba(255,255,255,.04);color:#cbd5e1;padding:0 15px;font-weight:700}.pay-table-wrap{overflow-x:auto}.pay-table{width:100%;border-collapse:collapse;min-width:940px}.pay-table th{padding:13px 16px;text-align:left;color:#8291a8;background:rgba(255,255,255,.018);font-size:.67rem;text-transform:uppercase;letter-spacing:.08em}.pay-table td{padding:15px 16px;border-top:1px solid rgba(148,163,184,.09);color:#cbd5e1;font-size:.77rem}.pay-person strong,.pay-person small{display:block}.pay-person strong{color:#f8fafc}.pay-person small{margin-top:4px;color:var(--ap-subtle)}.pay-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:999px;font-size:.68rem;font-weight:750}.pay-badge.green{color:#86efac;background:rgba(34,197,94,.09);border:1px solid rgba(34,197,94,.2)}.pay-badge.red{color:#fca5a5;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.18)}.pay-badge.blue{color:#93c5fd;background:rgba(59,130,246,.09);border:1px solid rgba(59,130,246,.18)}.pay-row-actions{display:flex;gap:7px}.pay-icon-btn{display:grid;width:34px;height:34px;place-items:center;border:1px solid var(--ap-border);border-radius:9px;background:rgba(255,255,255,.035);color:#bfdbfe;text-decoration:none}.pay-pagination{padding:15px 18px;border-top:1px solid var(--ap-border)}@media(max-width:1050px){.pay-stats{grid-template-columns:repeat(2,1fr)}.pay-filters{grid-template-columns:1fr 1fr}.pay-hero{flex-direction:column}}@media(max-width:650px){.pay-stats,.pay-filters{grid-template-columns:1fr}.pay-hero{padding:22px}.pay-actions .pay-btn{width:100%}}
</style>
@endpush

@section('content')
<div class="pay-page">
    <section class="pay-hero">
        <div>
            <h2>Suivi des paiements étudiants</h2>
            <p>Visualisez immédiatement qui a payé, la formule choisie (4 mois ou année), les dates de validité et tout l’historique des renouvellements.</p>
        </div>
        <div class="pay-actions">
            <a class="pay-btn" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Utilisateurs</a>
            <a class="pay-btn primary" href="{{ route('admin.student-payments.create') }}"><i class="bi bi-plus-circle-fill"></i> Enregistrer un paiement</a>
        </div>
    </section>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <section class="pay-stats">
        <div class="pay-stat"><span>Total étudiants</span><strong>{{ $totalStudents }}</strong></div>
        <div class="pay-stat"><span>Payés actuellement</span><strong>{{ $paidStudents }}</strong></div>
        <div class="pay-stat"><span>Non payés</span><strong>{{ $unpaidStudents }}</strong></div>
        <div class="pay-stat"><span>Année complète</span><strong>{{ $annualStudents }}</strong></div>
        <div class="pay-stat"><span>Formule 4 mois</span><strong>{{ $fourMonthStudents }}</strong></div>
    </section>

    <section class="pay-panel">
        <form class="pay-filters" method="GET" action="{{ route('admin.student-payments.index') }}">
            <input class="pay-control" type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom ou e-mail...">
            <select class="pay-control" name="payment_status">
                <option value="">Tous les statuts</option>
                <option value="paid" @selected(request('payment_status') === 'paid')>Payés</option>
                <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Non payés</option>
            </select>
            <select class="pay-control" name="plan_type">
                <option value="">Toutes les formules</option>
                <option value="annual" @selected(request('plan_type') === 'annual')>Année complète</option>
                <option value="four_months" @selected(request('plan_type') === 'four_months')>4 mois</option>
            </select>
            <button class="pay-filter-btn" type="submit"><i class="bi bi-funnel-fill"></i> Filtrer</button>
        </form>

        <div class="pay-table-wrap">
            <table class="pay-table">
                <thead><tr><th>Étudiant</th><th>Statut</th><th>Formule</th><th>Date paiement</th><th>Expiration</th><th>Montant</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($students as $student)
                    @php
                        $current = $student->studentPayments->first(fn($payment) => $payment->isCurrentlyValid());
                        $last = $student->studentPayments->first();
                        $legacyPaid = !$last && (bool)$student->is_paid;
                    @endphp
                    <tr>
                        <td><div class="pay-person"><strong>{{ $student->name }}</strong><small>{{ $student->email }}</small></div></td>
                        <td>
                            @if($current || $legacyPaid)<span class="pay-badge green"><i class="bi bi-check-circle-fill"></i> Payé</span>
                            @else<span class="pay-badge red"><i class="bi bi-x-circle-fill"></i> Non payé</span>@endif
                        </td>
                        <td>@if($current)<span class="pay-badge blue">{{ $current->plan_label }}</span>@elseif($legacyPaid)<span class="pay-badge blue">Ancien statut</span>@else — @endif</td>
                        <td>{{ $current ? optional($current->paid_at)->format('d/m/Y') : ($student->payment_date ? \Carbon\Carbon::parse($student->payment_date)->format('d/m/Y') : '—') }}</td>
                        <td>{{ $current ? optional($current->expires_at)->format('d/m/Y') : '—' }}</td>
                        <td>{{ $current && $current->amount !== null ? number_format((float)$current->amount, 2, ',', ' ') . ' DH' : '—' }}</td>
                        <td><div class="pay-row-actions">
                            <a class="pay-icon-btn" href="{{ route('admin.student-payments.show', $student) }}" title="Historique"><i class="bi bi-clock-history"></i></a>
                            <a class="pay-icon-btn" href="{{ route('admin.student-payments.create', ['student' => $student->id]) }}" title="Ajouter un paiement"><i class="bi bi-credit-card-fill"></i></a>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:#64748b">Aucun étudiant ne correspond aux filtres.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pay-pagination">{{ $students->links() }}</div>
    </section>
</div>
@endsection
