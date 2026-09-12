@extends('layouts.admin')

@section('title', 'Suivi des devoirs')
@section('page_title', 'Suivi des devoirs')
@section('breadcrumb', 'Pédagogie → Devoirs non remis')

@section('content')
<style>
.hw-page{--b:rgba(148,163,184,.12);--p:rgba(15,23,42,.78);--m:#94a3b8}
.hw-hero{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1.15rem 1.25rem;margin-bottom:1rem;border:1px solid var(--b);border-radius:20px;background:radial-gradient(circle at 95% 0%,rgba(139,92,246,.15),transparent 32%),linear-gradient(145deg,#111f36,#07101f)}
.hw-hero h1{margin:0;color:#fff;font-size:1.2rem}.hw-hero p{margin:.3rem 0 0;color:var(--m);font-size:.7rem}.hw-run{display:inline-flex;align-items:center;gap:.4rem;padding:.65rem .85rem;border:0;border-radius:12px;color:#fff;background:linear-gradient(135deg,#7c3aed,#2563eb);font-size:.63rem;font-weight:800}
.hw-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.7rem;margin-bottom:1rem}.hw-stat{padding:.8rem;border:1px solid var(--b);border-radius:15px;background:var(--p)}.hw-stat small{display:block;color:var(--m);font-size:.55rem}.hw-stat strong{display:block;margin-top:.15rem;color:#fff;font-size:1.1rem}
.hw-panel{overflow:hidden;margin-bottom:1rem;border:1px solid var(--b);border-radius:18px;background:var(--p)}.hw-head{padding:.85rem 1rem;border-bottom:1px solid var(--b)}.hw-head h2{margin:0;color:#fff;font-size:.8rem}.hw-head p{margin:.25rem 0 0;color:var(--m);font-size:.55rem}.hw-table-wrap{overflow:auto}.hw-table{width:100%;border-collapse:collapse}.hw-table th,.hw-table td{padding:.7rem .75rem;border-bottom:1px solid rgba(148,163,184,.08);text-align:left;vertical-align:top}.hw-table th{color:#8190a5;font-size:.49rem;text-transform:uppercase;letter-spacing:.05em}.hw-table td{color:#dbe4ee;font-size:.57rem}.hw-badge{display:inline-flex;padding:.28rem .45rem;border-radius:999px;font-size:.48rem;font-weight:800}.hw-badge.bad{color:#fecaca;background:rgba(239,68,68,.1)}.hw-badge.ok{color:#bbf7d0;background:rgba(34,197,94,.1)}.hw-empty{padding:2.5rem 1rem;text-align:center;color:#718096}
.hw-alert{margin-bottom:.8rem;padding:.7rem .85rem;border:1px solid rgba(34,197,94,.18);border-radius:12px;color:#bbf7d0;background:rgba(22,101,52,.15);font-size:.6rem}
@media(max-width:750px){.hw-hero{align-items:flex-start;flex-direction:column}.hw-stats{grid-template-columns:1fr}}
</style>

<div class="hw-page">
    @if(session('success'))
        <div class="hw-alert"><i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}</div>
    @endif

    <section class="hw-hero">
        <div>
            <h1><i class="bi bi-envelope-exclamation me-2"></i>Détection des devoirs non remis</h1>
            <p>Rappels automatiques aux étudiants et réclamations aux parents lorsque plusieurs devoirs sont manquants.</p>
        </div>
        <form method="POST" action="{{ route('admin.homework-reminders.run') }}" onsubmit="return confirm('Lancer la vérification et envoyer les réclamations maintenant ?')">
            @csrf
            <button type="submit" class="hw-run"><i class="bi bi-send-check-fill"></i> Vérifier et envoyer</button>
        </form>
    </section>

    <section class="hw-stats">
        <article class="hw-stat"><small>Devoirs manquants</small><strong>{{ $summary['missing'] }}</strong></article>
        <article class="hw-stat"><small>Étudiants concernés</small><strong>{{ $summary['students'] }}</strong></article>
        <article class="hw-stat"><small>Messages envoyés aujourd'hui</small><strong>{{ $summary['sent_today'] }}</strong></article>
    </section>

    <section class="hw-panel">
        <div class="hw-head"><h2>Devoirs actuellement non remis</h2><p>La détection se base sur Matière → Classe et la période du devoir.</p></div>
        @if($missing->count())
            <div class="hw-table-wrap"><table class="hw-table"><thead><tr><th>Étudiant</th><th>Devoir</th><th>Matière / Classe</th><th>Échéance</th><th>État</th></tr></thead><tbody>
            @foreach($missing as $item)
                <tr>
                    <td><strong>{{ $item->student->name }}</strong><br><span style="color:#718096">{{ $item->student->email }}</span></td>
                    <td><strong>{{ $item->assignment->title }}</strong></td>
                    <td>{{ optional($item->assignment->subject)->name ?? '—' }} → {{ optional($item->assignment->classRoom)->name ?? '—' }}</td>
                    <td>{{ $item->assignment->due_date ? $item->assignment->due_date->format('d/m/Y') : '—' }}</td>
                    <td><span class="hw-badge bad">Non remis</span></td>
                </tr>
            @endforeach
            </tbody></table></div>
        @else
            <div class="hw-empty"><i class="bi bi-check-circle d-block mb-2"></i>Aucun devoir en retard non remis.</div>
        @endif
    </section>

    <section class="hw-panel">
        <div class="hw-head"><h2>Historique des réclamations</h2><p>Un même rappel étudiant n'est pas renvoyé plusieurs fois pour le même devoir.</p></div>
        @if($logs->count())
            <div class="hw-table-wrap"><table class="hw-table"><thead><tr><th>Date</th><th>Étudiant</th><th>Destinataire</th><th>Type</th><th>Canal</th><th>Statut</th></tr></thead><tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ optional($log->sent_at)->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>{{ optional($log->student)->name ?? '—' }}</td>
                    <td>{{ optional($log->recipientUser)->name ?? $log->recipient ?? '—' }}</td>
                    <td>{{ $log->kind === \App\Models\AssignmentReminder::KIND_PARENT_MULTIPLE ? 'Réclamation parent (' . $log->missing_count . ' devoirs)' : 'Rappel étudiant' }}</td>
                    <td>{{ strtoupper($log->channel) }}</td>
                    <td><span class="hw-badge {{ $log->status === 'sent' ? 'ok' : 'bad' }}">{{ $log->status }}</span></td>
                </tr>
            @endforeach
            </tbody></table></div>
            <div style="padding:.8rem 1rem">{{ $logs->links() }}</div>
        @else
            <div class="hw-empty">Aucun message envoyé pour le moment.</div>
        @endif
    </section>
</div>
@endsection
