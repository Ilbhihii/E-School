@extends('layouts.prof')

@section('title', 'Gestion des Absences')
@section('page_title', 'Absences')
@section('breadcrumb', 'Gestion des présences')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-calendar-check me-2" style="color:var(--adm-primary);"></i> Gestion des Absences</h1>
        <div class="subtitle">Suivez et enregistrez les présences de vos étudiants</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('prof.absences.list') }}" class="adm-btn adm-btn-ghost">
            <i class="bi bi-clock-history me-1"></i> Historique
        </a>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">{{ session('success') }}</div>
@endif
@if(session('alert'))
<div class="adm-alert adm-alert-danger mb-4">{{ session('alert') }}</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-building" style="color:rgba(255,255,255,0.35);"></i> Sélection de classe</h4>
    </div>
    <div class="adm-card-body">
        <div class="adm-form-group">
            <label class="adm-form-label">Choisir une classe</label>
            <select id="classSelect" class="adm-form-select">
                <option value="">-- Sélectionnez une classe --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-people" style="color:rgba(255,255,255,0.35);"></i> Liste des étudiants</h4>
    </div>
    <div class="adm-card-body p-0">
        <form method="POST" action="{{ route('prof.absences.store') }}">
            @csrf
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th style="text-align:center;">Présent</th>
                            <th style="text-align:center;">Absent</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTable">
                        <tr>
                            <td colspan="3">
                                <div class="adm-empty" style="padding:3rem 2rem;">
                                    <div class="adm-empty-icon"><i class="bi bi-people-fill"></i></div>
                                    <h5>Aucune classe sélectionnée</h5>
                                    <p>Veuillez choisir une classe pour afficher la liste des étudiants</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:1.25rem 1.5rem;text-align:right;">
                <button type="submit" class="adm-btn adm-btn-success" id="submitBtn" disabled>
                    <i class="bi bi-check-circle-fill me-2"></i> Enregistrer les Absences
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.radio-label-prof {
    cursor: pointer;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    user-select: none;
    padding: 8px 16px;
    border-radius: 20px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.03);
}
.radio-label-prof:hover {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.8);
}
.radio-label-prof input:checked + span {
    color: white;
}
.radio-label-prof:has(input:checked) {
    background: var(--adm-gradient-primary);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(37,99,235,0.3);
}
.loading-spinner {
    border: 3px solid rgba(255,255,255,0.1);
    border-top: 3px solid #6366F1;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('classSelect');
    const studentsTable = document.getElementById('studentsTable');
    const submitBtn = document.getElementById('submitBtn');
    if (!classSelect || !studentsTable || !submitBtn) return;

    let originalStates = new Map();

    classSelect.addEventListener('change', function() {
        const classId = this.value;
        if (!classId) {
            studentsTable.innerHTML = `<tr><td colspan="3"><div class="adm-empty" style="padding:3rem 2rem;"><div class="adm-empty-icon"><i class="bi bi-people-fill"></i></div><h5>Sélectionnez une classe</h5><p>Choisissez une classe pour voir les étudiants</p></div></td></tr>`;
            submitBtn.disabled = true;
            originalStates.clear();
            return;
        }

        studentsTable.innerHTML = `<tr><td colspan="3" style="text-align:center;padding:2rem;"><div class="loading-spinner"></div><p style="color:rgba(255,255,255,0.4);">Chargement...</p></td></tr>`;

        fetch(`/prof/class-students/${classId}`)
            .then(res => { if (!res.ok) throw new Error('Erreur'); return res.json(); })
            .then(data => {
                originalStates.clear();
                if (!data || data.length === 0) {
                    studentsTable.innerHTML = `<tr><td colspan="3" style="text-align:center;padding:2rem;color:rgba(255,255,255,0.4);">Aucun étudiant dans cette classe</td></tr>`;
                    submitBtn.disabled = true;
                    return;
                }
                studentsTable.innerHTML = '';
                data.forEach(student => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><span style="font-weight:500;">${student.name}</span></td>
                        <td style="text-align:center;">
                            <label class="radio-label-prof">
                                <input type="radio" name="students[${student.id}]" value="1" checked style="display:none;">
                                <span>✅ Présent</span>
                            </label>
                        </td>
                        <td style="text-align:center;">
                            <label class="radio-label-prof">
                                <input type="radio" name="students[${student.id}]" value="0" style="display:none;">
                                <span>❌ Absent</span>
                            </label>
                        </td>
                    `;
                    originalStates.set(student.id, '1');
                    tr.querySelectorAll('input[type="radio"]').forEach(r => {
                        r.addEventListener('change', () => {
                            let changed = false;
                            for (let [id, state] of originalStates) {
                                const checked = document.querySelector(`input[name="students[${id}]"]:checked`);
                                if (checked && checked.value !== state) { changed = true; break; }
                            }
                            submitBtn.disabled = !changed;
                        });
                    });
                    studentsTable.appendChild(tr);
                });
                submitBtn.disabled = true;
            })
            .catch(() => {
                studentsTable.innerHTML = `<tr><td colspan="3" style="text-align:center;padding:2rem;color:var(--adm-danger);">Erreur de chargement</td></tr>`;
                submitBtn.disabled = true;
            });
    });

    submitBtn.addEventListener('click', function(e) {
        const count = originalStates.size;
        if (count > 0 && !confirm(`Confirmer l'enregistrement des absences pour ${count} étudiant(s) ?`)) e.preventDefault();
    });
});
</script>

@endsection
