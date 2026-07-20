@extends('layouts.admin')

@section('title', 'Rendez-vous — Administration')
@section('page_title', 'Rendez-vous')
@section('breadcrumb', 'Rendez-vous')

@section('content')

<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
}
.status-pending {
    background: rgba(251,146,60,0.15);
    color: #FB923C;
    border: 1px solid rgba(251,146,60,0.2);
}
.status-confirmed {
    background: rgba(34,197,94,0.15);
    color: #4ADE80;
    border: 1px solid rgba(34,197,94,0.2);
}
.status-cancelled {
    background: rgba(239,68,68,0.15);
    color: #FCA5A5;
    border: 1px solid rgba(239,68,68,0.2);
}
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
}
.type-test {
    background: rgba(124,58,237,0.15);
    color: #A78BFA;
    border: 1px solid rgba(124,58,237,0.2);
}
.type-information {
    background: rgba(6,182,212,0.15);
    color: #22D3EE;
    border: 1px solid rgba(6,182,212,0.2);
}
.type-communication {
    background: rgba(251,146,60,0.15);
    color: #FB923C;
    border: 1px solid rgba(251,146,60,0.2);
}
.type-other {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.6);
    border: 1px solid rgba(255,255,255,0.1);
}
.appointments-panel {
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    background: linear-gradient(145deg,rgba(255,255,255,0.045),rgba(255,255,255,0.018));
    box-shadow: 0 18px 50px rgba(0,0,0,0.18);
}
.appointments-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
}
.appointments-count {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: rgba(255,255,255,0.75);
    font-size: 0.84rem;
    font-weight: 600;
}
.appointments-count span {
    display: grid;
    place-items: center;
    min-width: 28px;
    height: 28px;
    padding: 0 8px;
    border-radius: 9px;
    background: rgba(124,58,237,0.18);
    color: #C4B5FD;
}
.appointments-search {
    position: relative;
    width: min(100%,320px);
}
.appointments-search i {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.3);
}
.appointments-search input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 11px;
    outline: none;
    background: rgba(255,255,255,0.035);
    color: #fff;
    font-size: 0.82rem;
}
.appointments-search input:focus { border-color: rgba(96,165,250,0.45); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.appointments-search input::placeholder { color: rgba(255,255,255,0.3); }
.student-name { display:flex;align-items:center;gap:10px;min-width:135px; }
.student-avatar { width:36px;height:36px;border-radius:11px;display:grid;place-items:center;flex:none;background:linear-gradient(135deg,#7C3AED,#2563EB);color:#fff;font-weight:700;box-shadow:0 6px 16px rgba(37,99,235,.2); }
.contact-link { display:flex;align-items:center;gap:7px;color:rgba(255,255,255,.68);text-decoration:none;white-space:nowrap; }
.contact-link:hover { color:#93C5FD; }
.path-pill { display:inline-flex;align-items:center;gap:5px;margin-top:5px;padding:3px 8px;border-radius:7px;background:rgba(255,255,255,.045);color:rgba(255,255,255,.48);font-size:.7rem; }
.audio-player { width:210px;height:36px;display:block;filter:invert(.88) hue-rotate(180deg);opacity:.86; }
@media(max-width:760px){.appointments-toolbar{align-items:stretch;flex-direction:column}.appointments-search{width:100%}.appointments-panel{border-radius:14px}}
</style>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <h3 style="color: rgba(255,255,255,0.6); font-weight: 400; font-size: 1rem;">
        <i class="bi bi-calendar-check me-2"></i>
        Rendez-vous de test Coran avec récitation vocale
    </h3>
</div>

<div class="appointments-panel">
    <div class="appointments-toolbar">
        <div class="appointments-count">
            <i class="bi bi-mic-fill" style="color:#A78BFA;"></i>
            Enregistrements reçus <span id="appointmentsCount">{{ $appointments->count() }}</span>
        </div>
        <label class="appointments-search">
            <i class="bi bi-search"></i>
            <input type="search" id="appointmentsSearch" placeholder="Rechercher un étudiant, une ville…" autocomplete="off">
        </label>
    </div>
<div class="table-responsive">
    <table class="table adm-table">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Parcours</th>
                <th>Récitation</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="appointmentsBody">
            @forelse($appointments as $appointment)
            <tr data-search="{{ mb_strtolower($appointment->first_name.' '.$appointment->last_name.' '.$appointment->email.' '.$appointment->phone.' '.$appointment->city.' '.$appointment->country.' '.$appointment->vocalSubmission->level->name.' '.$appointment->vocalSubmission->classRoom->name) }}">
                <td>
                    <div class="student-name">
                        <span class="student-avatar">{{ mb_strtoupper(mb_substr($appointment->first_name, 0, 1)) }}</span>
                        <span style="font-weight:600;">{{ $appointment->first_name }}</span>
                    </div>
                </td>
                <td>{{ $appointment->last_name }}</td>
                <td>
                    <a href="tel:{{ $appointment->phone }}" class="contact-link">
                        <i class="bi bi-telephone me-1" style="color: rgba(255,255,255,0.3);"></i>{{ $appointment->phone }}
                    </a>
                </td>
                <td>
                    <a href="mailto:{{ $appointment->email }}" class="contact-link">
                        <i class="bi bi-envelope me-1" style="color: rgba(255,255,255,0.3);"></i>{{ $appointment->email }}
                    </a>
                </td>
                <td>{{ $appointment->city ?: '—' }}</td>
                <td>{{ $appointment->country ?: '—' }}</td>
                <td style="min-width:170px;">
                    <strong>{{ $appointment->vocalSubmission->subject->name }}</strong><br>
                    <small class="path-pill">
                        <i class="bi bi-diagram-3"></i>
                        {{ $appointment->vocalSubmission->level->name }} · {{ $appointment->vocalSubmission->classRoom->name }}
                    </small>
                </td>
                <td style="min-width:240px;">
                    <audio controls preload="none" class="audio-player">
                        <source src="{{ route('admin.appointments.audio', $appointment) }}" type="{{ $appointment->vocalSubmission->audio_mime_type ?: 'audio/webm' }}">
                        Votre navigateur ne peut pas lire cet enregistrement.
                    </audio>
                </td>
                <td>
                    <span class="status-badge status-{{ $appointment->status }}">
                        <i class="bi {{ $appointment->status == 'pending' ? 'bi-clock' : ($appointment->status == 'confirmed' ? 'bi-check-circle' : 'bi-x-circle') }}"></i>
                        {{ $appointment->status == 'pending' ? 'En attente' : ($appointment->status == 'confirmed' ? 'Confirmé' : 'Annulé') }}
                    </span>
                </td>
                <td style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">
                    {{ $appointment->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if($appointment->status == 'pending')
                        <form method="POST" action="{{ route('admin.appointments.confirm', $appointment) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="adm-action-btn adm-action-edit" title="Confirmer">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.appointments.cancel', $appointment) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="adm-action-btn adm-action-danger" title="Annuler">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" class="d-inline"
                              onsubmit="return confirm('Supprimer ce rendez-vous ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="adm-action-btn adm-action-danger" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center py-5" style="color: rgba(255,255,255,0.3);">
                    <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    Aucun rendez-vous de test Coran avec enregistrement vocal
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>

<style>
.adm-table {
    width: 100%;
    border-collapse: collapse;
}
.adm-table thead th {
    background: rgba(255,255,255,0.03);
    padding: 12px 16px;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: rgba(255,255,255,0.35);
    font-weight: 600;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    position: sticky;
    top: 0;
    z-index: 2;
    backdrop-filter: blur(12px);
    white-space: nowrap;
}
.adm-table tbody td {
    padding: 14px 16px;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.75);
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.adm-table tbody tr:hover {
    background: rgba(96,165,250,0.045);
}
.adm-table tbody tr { transition:background .2s ease; }
.adm-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}
.adm-action-btn:hover {
    transform: translateY(-2px);
}
.adm-action-edit {
    background: rgba(6,182,212,0.15);
    color: #22D3EE;
}
.adm-action-edit:hover {
    background: rgba(6,182,212,0.25);
    box-shadow: 0 4px 12px rgba(6,182,212,0.2);
}
.adm-action-danger {
    background: rgba(239,68,68,0.15);
    color: #FCA5A5;
}
.adm-action-danger:hover {
    background: rgba(239,68,68,0.25);
    box-shadow: 0 4px 12px rgba(239,68,68,0.2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('appointmentsSearch');
    const rows = [...document.querySelectorAll('#appointmentsBody tr[data-search]')];
    const count = document.getElementById('appointmentsCount');
    search?.addEventListener('input', () => {
        const term = search.value.toLocaleLowerCase().trim();
        let visible = 0;
        rows.forEach(row => {
            const show = !term || row.dataset.search.includes(term);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        count.textContent = visible;
    });
});
</script>

@endsection
