@extends('layouts.admin')

@section('title', 'Gestion du Planning')
@section('page_title', 'Planning')
@section('breadcrumb', 'Gestion du planning')

@section('content')

<div class="adm-page-header">
    <div>
        <h1>Planning</h1>
        <div class="subtitle">Ajoutez et gérez les séances du planning</div>
    </div>
</div>

@if ($errors->any())
<div class="adm-alert adm-alert-danger">
    <span class="adm-alert-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
    <ul style="margin:0;padding-left:1.25rem;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row g-4">
    <!-- Add Form -->
    <div class="col-lg-5">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-calendar-plus" style="color:#4ADE80;"></i> Ajouter une séance</h4>
            </div>
            <div class="adm-card-body">
                <form method="POST" action="{{ route('admin.schedule.store') }}">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">Choisir une classe</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Professeur</label>
                        <select name="prof_id" class="adm-form-select" required>
                            <option value="">Choisir un professeur</option>
                            @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière</label>
                        <select name="subject" class="adm-form-select" required>
                            <option value="">Choisir une matière</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Début</label>
                                <input type="datetime-local" name="start_time" class="adm-form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Fin</label>
                                <input type="datetime-local" name="end_time" class="adm-form-control" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-primary w-100 mt-2">
                        <i class="bi bi-plus-lg"></i> Ajouter au planning
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="col-lg-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-calendar-week" style="color:rgba(255,255,255,0.35);"></i> Planning complet</h4>
                <div class="card-actions">
                    <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $schedules->count() }} séance(s)</span>
                </div>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Classe</th>
                                <th>Professeur</th>
                                <th>Matière</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $s)
                            <tr>
                                <td><span style="font-weight:500;">{{ $s->classRoom->name ?? 'N/A' }}</span></td>
                                <td>{{ $s->prof->name ?? 'N/A' }}</td>
                                <td><span class="adm-badge adm-badge-primary">{{ $s->subject }}</span></td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $s->date?->format('d/m/Y') ?? '-' }}</td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $s->start_time?->format('H:i') ?? '-' }} → {{ $s->end_time?->format('H:i') ?? '-' }}</td>
                                <td style="text-align:right;">
                                    <form method="POST" action="{{ route('admin.schedule.destroy', $s->id) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette séance ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="adm-empty">
                                        <div class="adm-empty-icon"><i class="bi bi-calendar"></i></div>
                                        <h5>Aucune séance</h5>
                                        <p>Ajoutez votre première séance dans le formulaire.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
