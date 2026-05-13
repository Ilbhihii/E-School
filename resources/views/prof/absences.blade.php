@extends('layouts.prof')

@section('content')
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
                <div class="card-body p-5">
                <div class="d-flex align-items-center mb-5">
                    <div class="bg-primary bg-gradient rounded-circle p-3 me-3 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar-check fs-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 text-dark fw-bold">Gestion des Absences</h2>
                        <p class="mb-0 text-muted">Suivez et enregistrez les présences de vos étudiants</p>
                    </div>
                </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('alert'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('alert') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
@endif

                <div class="text-end mb-3">
                    <a href="{{ route('prof.absences.list') }}" class="btn btn-dark">
                        <i class="bi bi-list"></i> Voir historique
                    </a>
                </div>

                <div class="mb-4">
Choisir une salle de classe <i class="bi bi-chevron-down ms-1"></i>
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-white border-end-0" style="border-radius: 15px 0 0 15px;">
                            <i class="bi bi-house-door text-primary"></i>
                        </span>
                        <select id="classSelect" class="form-select form-select-lg border-start-0 shadow-none" style="border-radius: 0 15px 15px 0; background: rgba(255,255,255,0.8);">
                            <option value="">-- Sélectionnez une classe --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


<form method="POST" action="{{ route('prof.absences.store') }}">
    @csrf



<div class="table-responsive shadow-lg rounded-3 overflow-hidden mb-4" style="background: rgba(255,255,255,0.7);">
    <table class="table table-hover mb-0" style="background: transparent;">

<thead>

<tr>
<th>Étudiant</th>
<th>Présent</th>
<th>Absent</th>
</tr>

</thead>

<tbody id="studentsTable">
                    <tr>
                        <td colspan="3" class="text-center text-muted py-5 bg-gradient rounded-3 mx-3 my-3" style="background: linear-gradient(135deg, rgba(233,236,239,0.8) 0%, rgba(108,117,125,0.3) 100%); border-radius: 15px;">
                            <div class="fs-1 mb-4 opacity-75"><i class="bi bi-people-fill text-primary"></i></div>
                            <h5 class="fw-semibold mb-2">Aucune classe sélectionnée</h5>
                            <p class="mb-0 lead">Veuillez choisir une classe pour afficher la liste des étudiants</p>
                        </td>
                    </tr>
    </tbody>
</table>

<div class="d-grid d-md-flex justify-content-md-end gap-2 mt-4">
                    <button type="submit" class="btn btn-lg btn-success px-5 py-3 fw-bold shadow-lg border-0" id="submitBtn" disabled style="border-radius: 50px; background: linear-gradient(45deg, #28a745, #20c997); transition: all 0.3s ease; min-width: 220px;">
                        <i class="bi bi-check-circle-fill me-2"></i>Enregistrer les Absences
                    </button>
</div>

</form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
.loading-spinner {
    border: 4px solid rgba(255,255,255,0.3);
    border-top: 4px solid #0d6efd;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 25px auto;
    box-shadow: 0 0 20px rgba(13,110,253,0.4);
}

.table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: none !important;
    font-weight: 600 !important;
    color: #495057 !important;
    padding: 20px 15px !important;
    border-radius: 12px 12px 0 0 !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.table tbody tr:hover {
    background: rgba(13,110,253,0.05) !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.radio-label {
    cursor: pointer;
    font-weight: 600;
    color: #6c757d;
    user-select: none;
    padding: 10px 15px;
    border-radius: 25px;
    transition: all 0.3s ease;
    display: block;
    text-align: center;
    background: rgba(255,255,255,0.6);
    border: 2px solid transparent;
    min-height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.radio-label:hover {
    background: rgba(255,255,255,0.9);
    color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(13,110,253,0.2);
}

.form-check-input:checked + span,
.radio-label:has(input:checked) {
    background: linear-gradient(135deg, #0d6efd, #6610f2) !important;
    color: white !important;
    box-shadow: 0 5px 25px rgba(13,110,253,0.4) !important;
    transform: scale(1.02);
}

#submitBtn:not(:disabled) {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
    box-shadow: 0 10px 30px rgba(40,167,69,0.4) !important;
    transform: translateY(-2px);
}

#submitBtn:not(:disabled):hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 40px rgba(40,167,69,0.6) !important;
}

.form-select:focus,
.btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15) !important;
    border-color: #0d6efd !important;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('classSelect');
    const studentsTable = document.getElementById('studentsTable');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!classSelect || !studentsTable || !submitBtn) {
        console.error('Required elements not found');
        return;
    }

    // Track original states to detect changes
    let originalStates = new Map();

    function showLoading() {
        studentsTable.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-5">
                    <div class="loading-spinner"></div>
                    <p class="mt-2 mb-0 text-muted">Chargement des étudiants...</p>
                </td>
            </tr>
        `;
    }

    function showNoStudents() {
        studentsTable.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-warning py-5">
                    <i class="bi bi-exclamation-triangle fs-2 mb-2 d-block"></i>
                    Aucun étudiant dans cette classe
                </td>
            </tr>
        `;
        submitBtn.disabled = true;
    }

    function showError(message) {
        studentsTable.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-danger py-5">
                    <i class="bi bi-exclamation-circle-fill fs-2 mb-2 d-block"></i>
                    ${message}
                </td>
            </tr>
        `;
        submitBtn.disabled = true;
    }

    function hasChanges() {
        for (let [studentId, originalState] of originalStates) {
            const currentRadios = document.querySelectorAll(`input[name="students[${studentId}]"]`);
            const currentState = Array.from(currentRadios).find(r => r.checked)?.value;
            if (currentState !== originalState) {
                return true;
            }
        }
        return false;
    }

    classSelect.addEventListener('change', function() {
        showLoading();

        const classId = this.value;
        if (!classId) {
            studentsTable.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-1 mb-3 d-block"></i>
                        Sélectionnez une classe pour voir les étudiants
                    </td>
                </tr>
            `;
            submitBtn.disabled = true;
            originalStates.clear();
            return;
        }

        fetch(`/prof/class-students/${classId}`)
            .then(res => {
                if (!res.ok) throw new Error('Erreur lors du chargement des étudiants');
                return res.json();
            })
            .then(data => {
                if (!data || data.length === 0) {
                    showNoStudents();
                    originalStates.clear();
                    return;
                }

                // Clear previous states
                originalStates.clear();

                // Create table rows safely
                while (studentsTable.firstChild) {
                    studentsTable.removeChild(studentsTable.firstChild);
                }

                data.forEach(student => {
                    const row = document.createElement('tr');
                    
                    // Student name - escape HTML
                    const nameCell = document.createElement('td');
                    nameCell.textContent = student.name;
                    row.appendChild(nameCell);

                    // Present radio
                    const presentCell = document.createElement('td');
                    const presentLabel = document.createElement('label');
                    presentLabel.className = 'radio-label d-block text-center';
                    const presentRadio = document.createElement('input');
                    presentRadio.type = 'radio';
                    presentRadio.name = `students[${student.id}]`;
                    presentRadio.value = '1';
                    presentRadio.checked = true;
                    presentRadio.className = 'form-check-input me-2';
                    presentLabel.appendChild(presentRadio);
                    presentLabel.appendChild(document.createTextNode('Présent'));
                    presentCell.appendChild(presentLabel);
                    row.appendChild(presentCell);

                    // Absent radio
                    const absentCell = document.createElement('td');
                    const absentLabel = document.createElement('label');
                    absentLabel.className = 'radio-label d-block text-center';
                    const absentRadio = document.createElement('input');
                    absentRadio.type = 'radio';
                    absentRadio.name = `students[${student.id}]`;
                    absentRadio.value = '0';
                    absentRadio.className = 'form-check-input me-2';
                    absentLabel.appendChild(absentRadio);
                    absentLabel.appendChild(document.createTextNode('Absent'));
                    absentCell.appendChild(absentLabel);
                    row.appendChild(absentCell);

                    // Store original state and add change listener
                    originalStates.set(student.id, '1');
                    const radios = row.querySelectorAll('input[type="radio"]');
                    radios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            submitBtn.disabled = !hasChanges();
                        });
                    });

                    studentsTable.appendChild(row);
                });

                // Defaults ready - enable button
                submitBtn.disabled = false;
            })

            .catch(error => {
                console.error('Erreur:', error);
                showError('Erreur de chargement des étudiants');
                originalStates.clear();
            });
    });


    // Add dynamic styling for radios
    const style = document.createElement('style');
    style.textContent = `
        .radio-label {
            cursor: pointer;
            font-weight: 500;
            color: #495057;
            user-select: none;
        }
        .radio-label:hover {
            color: #0d6efd;
        }
        .form-check-input:checked + * {
            font-weight: 600;
            color: #0d6efd !important;
        }
    `;
    document.head.appendChild(style);

    // Form submit confirmation
    submitBtn.addEventListener('click', function(e) {
        const numStudents = originalStates.size;
        if (numStudents === 0 || !confirm(`Confirmez-vous l'enregistrement des absences pour ${numStudents} étudiant(s) ?`)) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
