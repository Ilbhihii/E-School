@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📋 Historique des absences</span></h1>
                <p class="admin-header-subtitle">Suivi et modification des présences étudiants</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <!-- FILTER -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <form method="GET" class="adm-flex adm-gap-2 adm-flex-wrap">
                    <div class="adm-form-group" style="margin-bottom:0;flex:1;min-width:200px;">
                        <select name="class_id" class="adm-form-select">
                            <option value="">-- Toutes classes --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="adm-btn adm-btn-primary" type="submit"><i class="bi bi-search"></i> Filtrer</button>
                    <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost"><i class="bi bi-x-lg"></i> Reset</a>
                </form>
            </div>
        </div>

        @if($absences->count() > 0)
        <div class="adm-card">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Date</th>
                            <th>Classe</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                        <tr>
                            <td>
                                <div class="adm-user-cell">
                                    <div class="adm-avatar adm-avatar-sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                                        {{ strtoupper(substr($absence->user?->name ?? 'E',0,1)) }}
                                    </div>
                                    <span style="font-weight:600;">{{ $absence->user?->name ?? 'Étudiant inconnu' }}</span>
                                </div>
                            </td>
                            <td><span class="adm-badge adm-badge-gray">{{ $absence->created_at->format('d/m/Y H:i') }}</span></td>
                            <td><span class="adm-badge adm-badge-primary">{{ $absence->user->classRoom?->name ?? 'Non assigné' }}</span></td>
                            <td>
                                @if($absence->present)
                                    <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill"></i> Présent</span>
                                @else
                                    <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill"></i> Absent</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('prof.absences.update', $absence->id) }}" class="adm-flex adm-gap-1">
                                    @csrf @method('PUT')
                                    <select name="present" class="adm-form-select" style="width:auto;padding:0.3rem 0.6rem;font-size:0.8rem;">
                                        <option value="1" {{ $absence->present ? 'selected' : '' }}>Présent</option>
                                        <option value="0" {{ !$absence->present ? 'selected' : '' }}>Absent</option>
                                    </select>
                                    <button type="submit" class="adm-btn adm-btn-sm adm-btn-primary"><i class="bi bi-save"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(isset($absences))
            <div class="adm-card-footer">
                <div class="adm-pagination">{{ $absences->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>
        @else
        <div class="adm-card" style="text-align:center;padding:3rem;">
            <div class="adm-empty-icon" style="margin:0 auto 1.25rem;background:#dcfce7;color:#16a34a;">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3>🎉 Parfait !</h3>
            <p style="color:var(--adm-text-secondary);">Aucune absence enregistrée pour le moment.</p>
        </div>
        @endif

    </div>
</div>
@endsection
