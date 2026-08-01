@extends('layouts.prof')

@section('title', 'Gestion des Absences')
@section('page_title', 'Absences')
@section('breadcrumb', 'Gestion des présences')

@section('content')
<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-calendar-check me-2" style="color:var(--adm-primary);"></i> Gestion des Absences</h1>
        <div class="subtitle">Enregistrez les présences dans le parcours exact</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost"><i class="bi bi-clock-history me-1"></i> Historique</a>
    </div>
</div>

@if(session('success')) <div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div> @endif
@if(session('alert')) <div class="adm-alert adm-alert-danger mb-4">{{ session('alert') }}</div> @endif

<div class="adm-card">
    <div class="adm-card-header"><h4><i class="bi bi-diagram-3" style="color:rgba(255,255,255,0.35);"></i> Parcours et date</h4></div>
    <div class="adm-card-body">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="adm-form-label">Matière → Niveau → Classe</label>
                <select id="pathSelect" class="adm-form-select">
                    <option value="">-- Sélectionner un parcours --</option>
                    @foreach($profAssignments as $assignment)
                        @if($assignment->subject && $assignment->level && $assignment->classRoom)
                            <option
                                value="{{ $assignment->class_id }}"
                                data-subject="{{ $assignment->subject_id }}"
                                data-level="{{ $assignment->level_id }}"
                            >
                                {{ $assignment->subject->name }} → {{ $assignment->level->name }} → {{ $assignment->classRoom->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="adm-form-label" for="attendanceDate">Date</label>
                <input type="date" id="attendanceDate" class="adm-form-control" value="{{ now()->toDateString() }}">
            </div>
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header"><h4><i class="bi bi-people" style="color:rgba(255,255,255,0.35);"></i> Liste des étudiants</h4></div>
    <div class="adm-card-body p-0">
        <form method="POST" action="{{ route('prof.absences.store') }}" id="attendanceForm">
            @csrf
            <input type="hidden" name="subject_id" id="subjectId">
            <input type="hidden" name="level_id" id="levelId">
            <input type="hidden" name="class_id" id="classId">
            <input type="hidden" name="date" id="formDate" value="{{ now()->toDateString() }}">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead><tr><th>Étudiant</th><th style="text-align:center;">Présent</th><th style="text-align:center;">Absent</th></tr></thead>
                    <tbody id="studentsTable">
                        <tr><td colspan="3"><div class="adm-empty" style="padding:3rem 2rem;"><div class="adm-empty-icon"><i class="bi bi-people-fill"></i></div><h5>Aucun parcours sélectionné</h5><p>Choisissez une matière, un niveau et une classe.</p></div></td></tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:1.25rem 1.5rem;text-align:right;">
                <button type="submit" class="adm-btn adm-btn-success" id="submitBtn" disabled><i class="bi bi-check-circle-fill me-2"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<style>
.radio-label-prof { cursor:pointer; font-weight:500; color:rgba(255,255,255,.6); user-select:none; padding:8px 16px; border-radius:20px; transition:.2s; display:inline-flex; border:1px solid rgba(255,255,255,.06); background:rgba(255,255,255,.03); }
.radio-label-prof:has(input:checked) { background:var(--adm-gradient-primary); color:white; border-color:transparent; box-shadow:0 4px 15px rgba(37,99,235,.3); }
.loading-spinner { border:3px solid rgba(255,255,255,.1); border-top:3px solid #6366F1; border-radius:50%; width:40px; height:40px; animation:spin 1s linear infinite; margin:20px auto; }
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const pathSelect = document.getElementById('pathSelect');
    const dateInput = document.getElementById('attendanceDate');
    const formDate = document.getElementById('formDate');
    const subjectId = document.getElementById('subjectId');
    const levelId = document.getElementById('levelId');
    const classId = document.getElementById('classId');
    const table = document.getElementById('studentsTable');
    const button = document.getElementById('submitBtn');

    dateInput.addEventListener('change', () => formDate.value = dateInput.value);

    pathSelect.addEventListener('change', async () => {
        const selected = pathSelect.options[pathSelect.selectedIndex];
        const selectedClass = selected.value;
        subjectId.value = selected.dataset.subject || '';
        levelId.value = selected.dataset.level || '';
        classId.value = selectedClass;
        button.disabled = true;

        if (!selectedClass) {
            table.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:2rem;">Choisissez un parcours.</td></tr>';
            return;
        }

        table.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:2rem;"><div class="loading-spinner"></div></td></tr>';

        try {
            const params = new URLSearchParams({ subject_id: subjectId.value, level_id: levelId.value });
            const response = await fetch(`/prof/class-students/${selectedClass}?${params.toString()}`);
            if (!response.ok) throw new Error();
            const students = await response.json();

            if (!students.length) {
                table.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:2rem;">Aucun étudiant assigné à ce parcours.</td></tr>';
                return;
            }

            table.innerHTML = students.map(student => `
                <tr>
                    <td><strong>${student.name}</strong></td>
                    <td style="text-align:center;"><label class="radio-label-prof"><input type="radio" name="students[${student.id}]" value="1" checked style="display:none;"><span>Présent</span></label></td>
                    <td style="text-align:center;"><label class="radio-label-prof"><input type="radio" name="students[${student.id}]" value="0" style="display:none;"><span>Absent</span></label></td>
                </tr>
            `).join('');
            button.disabled = false;
        } catch (error) {
            table.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--adm-danger);">Erreur de chargement.</td></tr>';
        }
    });
});
</script>
@endsection
