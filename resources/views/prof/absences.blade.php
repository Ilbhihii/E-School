@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Gestion des Absences</span></h1>
                <p class="admin-header-subtitle">Suivez et enregistrez les présences de vos étudiants</p>
            </div>
            <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-list"></i> Voir historique
            </a>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('alert'))
            <div class="adm-alert adm-alert-danger">{{ session('alert') }}</div>
        @endif

        <!-- CLASS SELECT -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <div class="adm-flex adm-gap-2" style="align-items:flex-end;">
                    <div class="adm-form-group" style="margin-bottom:0;flex:1;">
                        <label class="adm-form-label"><i class="bi bi-house-door"></i> Choisir une classe</label>
                        <select id="classSelect" class="adm-form-select">
                            <option value="">-- Sélectionnez une classe --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('prof.absences.store') }}">
            @csrf

            <div class="adm-card">
                <div class="adm-table-wrap">
                    <table class="adm-table" id="absencesTable">
                        <thead>
                            <tr>
                                <th>Étudiant</th>
                                <th style="text-align:center;">Présent</th>
                                <th style="text-align:center;">Absent</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTable">
                            <tr>
                                <td colspan="3" class="adm-empty" style="padding:3rem;">
                                    <div class="adm-empty-icon"><i class="bi bi-people-fill"></i></div>
                                    <h3>Aucune classe sélectionnée</h3>
                                    <p>Veuillez choisir une classe pour afficher la liste des étudiants</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="adm-card-footer">
                    <button type="submit" class="adm-btn adm-btn-success adm-btn-lg" id="submitBtn" disabled style="width:100%;">
                        <i class="bi bi-check-circle-fill"></i> Enregistrer les Absences
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<style>
.radio-label { cursor: pointer; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: #f9fafb; border: 2px solid transparent; }
.radio-label:hover { background: #eef2ff; color: #6366f1; }
.radio-label input:checked + span, .radio-label:has(input:checked) { background: #6366f1 !important; color: white !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('classSelect');
    const studentsTable = document.getElementById('studentsTable');
    const submitBtn = document.getElementById('submitBtn');
    let originalStates = new Map();
    let hasChangesMap = new Map();

    function loadStudents(classId) {
        if (!classId) {
            studentsTable.innerHTML = `<tr><td colspan="3" class="adm-empty" style="padding:3rem;"><div class="adm-empty-icon"><i class="bi bi-people-fill"></i></div><h3>Sélectionnez une classe</h3></td></tr>`;
            submitBtn.disabled = true;
            return;
        }
        studentsTable.innerHTML = `<tr><td colspan="3" class="adm-empty" style="padding:3rem;"><div class="loading-spinner" style="border:3px solid #e5e7eb;border-top:3px solid #6366f1;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:0 auto 1rem;"></div><p>Chargement...</p></td></tr>`;
        
        fetch(`/prof/class-students/${classId}`)
            .then(res => res.json())
            .then(data => {
                if (!data || data.length === 0) {
                    studentsTable.innerHTML = `<tr><td colspan="3" class="adm-empty">Aucun étudiant dans cette classe</td></tr>`;
                    submitBtn.disabled = true;
                    return;
                }
                originalStates.clear();
                hasChangesMap.clear();
                let html = '';
                data.forEach(student => {
                    originalStates.set(student.id, '1');
                    hasChangesMap.set(student.id, false);
                    html += `<tr>
                        <td><div class="adm-user-cell"><div class="adm-avatar adm-avatar-sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">${student.name.charAt(0).toUpperCase()}</div><span style="font-weight:600;">${student.name}</span></div></td>
                        <td style="text-align:center;"><label class="radio-label"><input type="radio" name="students[${student.id}]" value="1" checked><span>Présent</span></label></td>
                        <td style="text-align:center;"><label class="radio-label"><input type="radio" name="students[${student.id}]" value="0"><span>Absent</span></label></td>
                    </tr>`;
                });
                studentsTable.innerHTML = html;
                submitBtn.disabled = false;
                
                document.querySelectorAll('input[type="radio"]').forEach(r => {
                    r.addEventListener('change', function() {
                        const sid = this.name.match(/\d+/)[0];
                        const prevState = originalStates.get(parseInt(sid));
                        hasChangesMap.set(parseInt(sid), this.value !== prevState);
                        const hasChanges = Array.from(hasChangesMap.values()).some(v => v);
                        submitBtn.disabled = !hasChanges;
                    });
                });
            })
            .catch(() => {
                studentsTable.innerHTML = `<tr><td colspan="3" class="adm-empty" style="color:var(--adm-danger);">Erreur de chargement</td></tr>`;
                submitBtn.disabled = true;
            });
    }

    classSelect.addEventListener('change', function() { loadStudents(this.value); });
    
    submitBtn.addEventListener('click', function(e) {
        const checked = Array.from(document.querySelectorAll('input[type="radio"]:checked')).length;
        if (checked === 0 || !confirm(`Confirmez-vous l'enregistrement des absences ?`)) {
            e.preventDefault();
        }
    });
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
@endsection
