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
</style>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <h3 style="color: rgba(255,255,255,0.6); font-weight: 400; font-size: 1rem;">
        <i class="bi bi-calendar-check me-2"></i>
        Toutes les demandes de rendez-vous
    </h3>
</div>

<div class="table-responsive">
    <table class="table adm-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
            <tr>
                <td style="color: rgba(255,255,255,0.3);">{{ $appointment->id }}</td>
                <td style="font-weight: 500;">{{ $appointment->first_name }}</td>
                <td>{{ $appointment->last_name }}</td>
                <td>
                    <a href="tel:{{ $appointment->phone }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">
                        <i class="bi bi-telephone me-1" style="color: rgba(255,255,255,0.3);"></i>{{ $appointment->phone }}
                    </a>
                </td>
                <td>
                    <a href="mailto:{{ $appointment->email }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">
                        <i class="bi bi-envelope me-1" style="color: rgba(255,255,255,0.3);"></i>{{ $appointment->email }}
                    </a>
                </td>
                <td>
                    <span class="type-badge type-{{ $appointment->type }}">
                        @if($appointment->type === 'test')
                            <i class="bi bi-pencil-square"></i>
                        @elseif($appointment->type === 'information')
                            <i class="bi bi-info-circle"></i>
                        @elseif($appointment->type === 'communication')
                            <i class="bi bi-chat-dots"></i>
                        @else
                            <i class="bi bi-three-dots"></i>
                        @endif
                        {{ $appointment->type_label }}
                    </span>
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
                <td colspan="9" class="text-center py-5" style="color: rgba(255,255,255,0.3);">
                    <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    Aucun rendez-vous pour le moment
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
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
}
.adm-table tbody td {
    padding: 14px 16px;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.75);
    border-bottom: 1px solid rgba(255,255,255,0.03);
}
.adm-table tbody tr:hover {
    background: rgba(255,255,255,0.02);
}
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

@endsection
