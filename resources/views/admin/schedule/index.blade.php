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

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
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

                    <!-- Matière -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière</label>
                        <select name="subject" class="adm-form-select" required>
                            <option value="">Choisir une matière</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Niveaux -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Niveau</label>
                        <select name="level_id" id="schedule_level_id" class="adm-form-select" required>
                            <option value="">Choisir un niveau</option>
                            @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Classe -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" id="schedule_class_id" class="adm-form-select" required>
                            <option value="">D'abord choisir un niveau</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-level="{{ $c->level_id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Professeur -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Professeur</label>
                        <select name="prof_id" class="adm-form-select" required>
                            <option value="">Choisir un professeur</option>
                            @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">Date</label>
                        <input type="date" name="date" class="adm-form-control" required>
                    </div>

                    <!-- Début / Fin -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Début</label>
                                <input type="time" name="start_time" class="adm-form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Fin</label>
                                <input type="time" name="end_time" class="adm-form-control" required>
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
                                <th>Matière</th>
                                <th>Niveau</th>
                                <th>Classe</th>
                                <th>Professeur</th>
                                <th>Date</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $s)
                            <tr>
                                <td><span class="adm-badge adm-badge-primary">{{ $s->subject }}</span></td>
                                <td>{{ $s->classRoom?->level?->name ?? '-' }}</td>
                                <td><span style="font-weight:500;">{{ $s->classRoom->name ?? 'N/A' }}</span></td>
                                <td>{{ $s->prof->name ?? 'N/A' }}</td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $s->date?->format('d/m/Y') ?? '-' }}</td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $s->start_time?->format('H:i') ?? '-' }}</td>
                                <td style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $s->end_time?->format('H:i') ?? '-' }}</td>
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
                                <td colspan="8">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('schedule_level_id');
    const classSelect = document.getElementById('schedule_class_id');

    function filterClasses() {
        const selectedLevel = levelSelect.value;
        const options = classSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (opt.value === '') {
                opt.hidden = false;
                opt.text = selectedLevel ? 'Choisir une classe' : 'D\'abord choisir un niveau';
                return;
            }
            const level = opt.getAttribute('data-level');
            opt.hidden = (level !== selectedLevel);
        });

        // Reset selection if hidden
        if (classSelect.selectedOptions[0]?.hidden) {
            classSelect.value = '';
        }
    }

    levelSelect.addEventListener('change', filterClasses);
    filterClasses();
});
</script>

@endsection
