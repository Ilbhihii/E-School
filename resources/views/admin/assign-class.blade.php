@extends('layouts.admin')

@section('title', 'Assignation')
@section('page_title', 'Assignation')
@section('breadcrumb', 'Assigner des étudiants')

@section('content')

<div class="row g-4">
    <!-- Assign Form -->
    <div class="col-lg-5">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-person-plus" style="color:#4ADE80;"></i> Nouvelle assignation</h4>
            </div>
            <div class="adm-card-body">
                @if(session('success'))
                <div class="adm-alert adm-alert-success mb-4">
                    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.assign.class.store') }}">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">Étudiant</label>
                        <select name="user_id" class="adm-form-select" required>
                            <option value="">Sélectionner un étudiant</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select" required>
                            <option value="">Sélectionner une classe</option>
                            @foreach($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="adm-btn adm-btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Assigner à la classe
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="col-lg-7">
        <div class="adm-card">
            <div class="adm-card-header">
                <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Assignations existantes</h4>
                <div class="card-actions">
                    <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ isset($assignments) ? count($assignments) : 0 }} assignation(s)</span>
                </div>
            </div>
            <div class="adm-card-body p-0">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Étudiant</th>
                                <th>Classe</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments ?? [] as $assignment)
                            <tr>
                                <td style="color:var(--adm-text-muted);">{{ $assignment->user_id }}</td>
                                <td><span style="font-weight:500;">{{ $assignment->student_name }}</span></td>
                                <td><span class="adm-badge adm-badge-primary">{{ $assignment->class_name }}</span></td>
                                <td style="text-align:right;">
                                    <div style="display:flex;gap:6px;justify-content:flex-end;">
                                        <button class="adm-btn adm-btn-warning adm-btn-sm" onclick="editAssignment({{ $assignment->user_id }}, {{ $assignment->class_id }}, {{ $assignment->pivot_id }})">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <form method="POST" action="{{ route('admin.assign.class.destroy',$assignment->pivot_id) }}" style="display:inline;" onsubmit="return confirm('Supprimer cette assignation ?')">
                                            @csrf @method('DELETE')
                                            <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="adm-empty">
                                        <div class="adm-empty-icon"><i class="bi bi-people"></i></div>
                                        <h5>Aucune assignation</h5>
                                        <p>Assignez un étudiant à une classe via le formulaire.</p>
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

<!-- Edit Modal -->
<div class="adm-modal-overlay" id="editModal" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
    <div class="adm-modal">
        <form method="POST" action="{{ route('admin.assign.class.update', '__PIVOT_ID__') }}" id="editForm">
            @csrf @method('PATCH')
            <input type="hidden" name="pivot_id" id="edit_assignment_id">
            <div class="adm-modal-header">
                <h5><i class="bi bi-pencil me-2"></i> Modifier l'assignation</h5>
                <button type="button" class="adm-modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="adm-modal-body">
                <div class="adm-form-group">
                    <label class="adm-form-label">Étudiant</label>
                    <select name="user_id" id="edit_user_id" class="adm-form-select" required>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="adm-form-group">
                    <label class="adm-form-label">Classe</label>
                    <select name="class_id" id="edit_class_id" class="adm-form-select" required>
                        @foreach($classRooms as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="adm-modal-footer">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="adm-btn adm-btn-primary">Modifier</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAssignment(userId, classId, pivotId) {
    document.getElementById('edit_assignment_id').value = pivotId;
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_class_id').value = classId;
    const form = document.getElementById('editForm');
    form.action = form.action.replace('__PIVOT_ID__', pivotId);
    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}
</script>
@endsection
