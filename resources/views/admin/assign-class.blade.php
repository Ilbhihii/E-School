@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">👥 Assignation d'étudiants</span></h1>
                <p class="admin-header-subtitle">Gérez les assignations étudiants-classes facilement</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if(isset($assignments) && count($assignments) > 0)
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);">
                <h3><i class="bi bi-people-fill"></i> Étudiants assignés</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom étudiant</th>
                            <th>Classe</th>
                            <th style="width:240px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td><span class="adm-badge adm-badge-gray">#{{ $assignment->user_id }}</span></td>
                            <td><span style="font-weight:600;">{{ $assignment->student_name }}</span></td>
                            <td>
                                <span class="adm-badge" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;border:none;">
                                    {{ $assignment->class_name }}
                                </span>
                            </td>
                            <td>
                                <div class="adm-actions">
                                    <button class="adm-action-link adm-action-edit" style="border:none;cursor:pointer;" onclick="editAssignment({{ $assignment->user_id }}, {{ $assignment->class_id }}, {{ $assignment->pivot_id }})">
                                        <i class="bi bi-pencil-fill"></i> Modifier
                                    </button>
                                    <form method="POST" action="{{ route('admin.assign.class.destroy',$assignment->pivot_id) }}" onsubmit="return confirm('Supprimer cette assignation ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                            <i class="bi bi-trash-fill"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- EDIT MODAL -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 25px 60px rgba(15,23,42,0.2);">
                    <div class="modal-header" style="border-bottom:1px solid var(--adm-border);padding:1.25rem 1.5rem;">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-fill text-indigo-600"></i> Modifier l'assignation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.assign.class.update', '__PIVOT_ID__') }}" id="editForm">
                        @csrf @method('PATCH')
                        <input type="hidden" name="pivot_id" id="edit_assignment_id">
                        <div class="modal-body" style="padding:1.5rem;">
                            <div class="adm-form-group">
                                <label class="adm-form-label">Étudiant</label>
                                <select name="user_id" id="edit_user_id" class="adm-form-select">
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="adm-form-group">
                                <label class="adm-form-label">Classe</label>
                                <select name="class_id" id="edit_class_id" class="adm-form-select">
                                    @php $classRooms = \App\Models\ClassRoom::all(); @endphp
                                    @foreach($classRooms as $classRoom)
                                        <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top:1px solid var(--adm-border);padding:1rem 1.5rem;">
                            <button type="button" class="adm-btn adm-btn-ghost" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="adm-btn adm-btn-primary">Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- NEW ASSIGNMENT -->
        <div class="adm-card">
            <div class="adm-card-body" style="text-align:center;">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                    <i class="bi bi-plus-lg" style="font-size:1.75rem;color:white;"></i>
                </div>
                <h3 style="font-size:1.25rem;font-weight:700;color:var(--adm-text);margin:0 0 0.5rem;">Nouvelle assignation</h3>
                <p style="color:var(--adm-text-secondary);margin:0 0 1.5rem;">Assignez un étudiant à une classe</p>

                <form method="POST" action="{{ route('admin.assign.class.store') }}" style="max-width:500px;margin:0 auto;text-align:left;">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">Étudiant</label>
                        <select name="user_id" class="adm-form-select">
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select">
                            @foreach($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg" style="width:100%;">
                        <i class="bi bi-plus-lg"></i> Assigner à la classe
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function editAssignment(userId, classId, pivotId) {
    document.getElementById('edit_assignment_id').value = pivotId;
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_class_id').value = classId;

    const form = document.getElementById('editForm');
    form.action = form.action.replace('__PIVOT_ID__', pivotId);

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>
@endsection
