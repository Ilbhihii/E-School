@extends('layouts.admin')

@section('title', 'Assignation')
@section('page_title', 'Assignation')
@section('breadcrumb', 'Assigner des étudiants')

@section('content')

<style>
.cascade-step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 12px;
    background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.06);
    margin-bottom: 10px;
    transition: all 0.3s ease;
}
.cascade-step.active {
    border-color: rgba(124,58,237,0.3);
    background: rgba(124,58,237,0.04);
}
.cascade-step .step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
    transition: all 0.3s ease;
}
.cascade-step.active .step-num {
    transform: scale(1.1);
}
.cascade-step .step-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--adm-text-muted);
    margin-bottom: 2px;
}
.cascade-step .step-value {
    font-size: 0.85rem;
    font-weight: 600;
    color: rgba(255,255,255,0.7);
}
.cascade-step.active .step-value {
    color: rgba(255,255,255,0.92);
}
.cascade-arrow {
    color: var(--adm-text-muted);
    font-size: 1rem;
    opacity: 0.5;
    flex-shrink: 0;
    padding: 0 4px;
}
</style>

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

                @if(session('error'))
                <div class="adm-alert mb-4" style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#FCA5A5;border-radius:12px;padding:12px 16px;">
                    <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                </div>
                @endif

                <!-- ═══ CASCADE VISUAL INDICATOR ═══ -->
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:1rem;padding:8px 12px;background:rgba(255,255,255,0.02);border-radius:10px;">
                    <div style="flex:1;text-align:center;padding:6px 0;border-radius:8px;background:rgba(124,58,237,0.15);color:#A78BFA;font-size:0.75rem;font-weight:600;">
                        <i class="bi bi-layers me-1"></i> Niveau
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--adm-text-muted);font-size:0.7rem;"></i>
                    <div style="flex:1;text-align:center;padding:6px 0;border-radius:8px;background:rgba(5,150,105,0.15);color:#34D399;font-size:0.75rem;font-weight:600;">
                        <i class="bi bi-building me-1"></i> Classe
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--adm-text-muted);font-size:0.7rem;"></i>
                    <div style="flex:1;text-align:center;padding:6px 0;border-radius:8px;background:rgba(217,119,6,0.15);color:#FBBF24;font-size:0.75rem;font-weight:600;">
                        <i class="bi bi-book me-1"></i> Matière
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.assign.class.store') }}">
                    @csrf
                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            <i class="bi bi-person me-1" style="color:#4ADE80;"></i> Étudiant
                        </label>
                        <select name="user_id" class="adm-form-select" required>
                            <option value="">Sélectionner un étudiant</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="height:12px;"></div>

                    <!-- Step 1: Level -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            <i class="bi bi-layers me-1" style="color:#A78BFA;"></i> 1. Niveau
                        </label>
                        <select class="adm-form-select" id="level_filter">
                            <option value="">Choisir un niveau</option>
                            @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 2: Class -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            <i class="bi bi-building me-1" style="color:#34D399;"></i> 2. Classe
                        </label>
                        <select name="class_id" class="adm-form-select" id="class_id" required>
                            <option value="">D'abord choisir un niveau</option>
                            @foreach($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}" data-level-id="{{ $classRoom->level_id }}">{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 3: Subjects (checkboxes multiples) -->
                    <div class="adm-form-group">
                        <label class="adm-form-label">
                            <i class="bi bi-book me-1" style="color:#FBBF24;"></i> 3. Matières
                        </label>
                        <div id="subjects_container" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:10px;padding:0.75rem;max-height:240px;overflow-y:auto;">
                            <div class="subject-placeholder" style="text-align:center;padding:1.5rem;color:var(--adm-text-muted);font-size:0.85rem;">
                                <i class="bi bi-inbox me-1"></i> D'abord choisir une classe
                            </div>
                            @foreach($subjects as $subject)
                                @php
                                    $classIds = $subject->classes->pluck('id')->implode(',');
                                @endphp
                                <label class="subject-checkbox-item" data-class-ids="{{ $classIds }}" style="display:none;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;transition:background 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" style="width:16px;height:16px;accent-color:#FBBF24;cursor:pointer;flex-shrink:0;">
                                    <span style="font-size:0.88rem;color:rgba(255,255,255,0.8);">{{ $subject->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small style="color:var(--adm-text-muted);font-size:0.75rem;margin-top:4px;display:block;">
                            <i class="bi bi-info-circle me-1"></i> Cochez une ou plusieurs matières. Seules les matières liées à la classe sélectionnée apparaissent.
                        </small>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary w-100" style="margin-top:8px;">
                        <i class="bi bi-plus-lg me-1"></i> Assigner les matières sélectionnées
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
                                <th>Étudiant</th>
                                <th>Classe</th>
                                <th>Matière</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments ?? [] as $assignment)
                            <tr>
                                <td><span style="font-weight:500;">{{ $assignment->student_name }}</span></td>
                                <td><span class="adm-badge adm-badge-primary">{{ $assignment->class_name }}</span></td>
                                <td>
                                    @if($assignment->subject_name)
                                        <span class="adm-badge" style="background:rgba(217,119,6,0.15);color:#FBBF24;">{{ $assignment->subject_name }}</span>
                                    @else
                                        <span style="color:var(--adm-text-muted);font-size:0.8rem;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;gap:6px;justify-content:flex-end;">
                                        <button class="adm-btn adm-btn-warning adm-btn-sm" onclick="editAssignment({{ $assignment->user_id }}, {{ $assignment->class_id }}, {{ $assignment->subject_id ?? 'null' }}, {{ $assignment->pivot_id }})">
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
                    <label class="adm-form-label">Niveau</label>
                    <select class="adm-form-select" id="edit_level_filter">
                        <option value="">Sélectionner un niveau</option>
                        @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label">Classe</label>
                    <select name="class_id" id="edit_class_id" class="adm-form-select" required>
                        <option value="">D'abord choisir un niveau</option>
                        @foreach($classRooms as $c)
                        <option value="{{ $c->id }}" data-level-id="{{ $c->level_id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="adm-form-group">
                    <label class="adm-form-label">Matière <span style="color:var(--adm-text-muted);font-size:0.75rem;">(optionnelle)</span></label>
                    <select name="subject_id" id="edit_subject_id" class="adm-form-select">
                        <option value="">Aucune matière spécifique</option>
                        @foreach($subjects as $s)
                            @php $classIds = $s->classes->pluck('id')->implode(','); @endphp
                        <option value="{{ $s->id }}" data-class-ids="{{ $classIds }}">{{ $s->name }}</option>
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
// 🔄 Filter classes by level
function filterClassesByLevel(filterSelectId, classSelectId, subjectSelectId, preserveSubject) {
    const filter = document.getElementById(filterSelectId);
    const classSelect = document.getElementById(classSelectId);
    const levelId = filter.value;

    let hasVisible = false;
    Array.from(classSelect.options).forEach(function(opt) {
        if (!opt.value) {
            opt.text = levelId ? 'Choisir une classe' : 'D\'abord choisir un niveau';
            opt.style.display = '';
            return;
        }
        if (!levelId || opt.dataset.levelId == levelId) {
            opt.style.display = '';
            hasVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });

    // Reset class selection if hidden
    if (classSelect.selectedIndex > 0) {
        const selected = classSelect.options[classSelect.selectedIndex];
        if (selected.style.display === 'none') {
            classSelect.value = '';
        }
    }

    // Also filter subjects when class changes (skip if subjectSelectId is null = checkboxes mode)
    if (!preserveSubject && subjectSelectId) {
        filterSubjectsByClass(classSelectId, subjectSelectId);
    } else if (!preserveSubject && !subjectSelectId) {
        filterSubjectCheckboxesByClass(classSelectId, 'subjects_container');
    }
}

// 🔄 Filter subject checkboxes by class (main form with multiple selection)
function filterSubjectCheckboxesByClass(classSelectId, containerId) {
    const classSelect = document.getElementById(classSelectId);
    const container = document.getElementById(containerId);
    const classId = classSelect.value;

    let hasVisible = false;
    container.querySelectorAll('.subject-checkbox-item').forEach(function(item) {
        const classIds = (item.dataset.classIds || '').split(',').filter(Boolean);
        if (!classId || classIds.includes(classId)) {
            item.style.display = 'flex';
            hasVisible = true;
        } else {
            item.style.display = 'none';
            // Uncheck hidden items
            const cb = item.querySelector('input[type="checkbox"]');
            if (cb) cb.checked = false;
        }
    });

    // Show/hide placeholder
    const placeholder = container.querySelector('.subject-placeholder');
    if (placeholder) {
        placeholder.style.display = (!classId || hasVisible) ? '' : 'none';
    }

    // Show/hide no-match message
    let noMatch = container.querySelector('.subject-no-match');
    if (hasVisible || !classId) {
        if (noMatch) noMatch.style.display = 'none';
    } else {
        if (!noMatch) {
            noMatch = document.createElement('div');
            noMatch.className = 'subject-no-match';
            noMatch.style.cssText = 'text-align:center;padding:1rem;color:var(--adm-text-muted);font-size:0.82rem;';
            noMatch.innerHTML = '<i class="bi bi-search me-1"></i> Aucune matière liée à cette classe';
            container.appendChild(noMatch);
        } else {
            noMatch.style.display = '';
        }
    }
}

// 🔄 Filter subjects by class (via data-class-ids on option) — used by edit modal (single select)
function filterSubjectsByClass(classSelectId, subjectSelectId) {
    const classSelect = document.getElementById(classSelectId);
    const subjectSelect = document.getElementById(subjectSelectId);
    const classId = classSelect.value;

    let hasVisible = false;
    Array.from(subjectSelect.options).forEach(function(opt) {
        if (!opt.value) {
            opt.text = classId ? 'Choisir une matière' : 'D\'abord choisir une classe';
            opt.style.display = '';
            return;
        }
        const classIds = (opt.dataset.classIds || '').split(',').filter(Boolean);
        if (!classId || classIds.includes(classId)) {
            opt.style.display = '';
            hasVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });

    // Reset subject selection if hidden
    if (subjectSelect.selectedIndex > 0) {
        const selected = subjectSelect.options[subjectSelect.selectedIndex];
        if (selected.style.display === 'none') {
            subjectSelect.value = '';
        }
    }
}

// ✏️ Edit assignment
function editAssignment(userId, classId, subjectId, pivotId) {
    document.getElementById('edit_assignment_id').value = pivotId;
    document.getElementById('edit_user_id').value = userId;

    // Find class to get its level
    const classOption = document.querySelector('#edit_class_id option[value="' + classId + '"]');
    let levelId = '';
    if (classOption && classOption.dataset.levelId) {
        levelId = classOption.dataset.levelId;
    }

    // Set level filter
    document.getElementById('edit_level_filter').value = levelId;

    // Filter classes by level, but preserve the selected subject
    filterClassesByLevel('edit_level_filter', 'edit_class_id', 'edit_subject_id', true);

    // Set class value
    document.getElementById('edit_class_id').value = classId;

    // Filter subjects by class
    filterSubjectsByClass('edit_class_id', 'edit_subject_id');

    // Set subject value (null → empty string for the select)
    document.getElementById('edit_subject_id').value = subjectId || '';

    const form = document.getElementById('editForm');
    form.action = form.action.replace('__PIVOT_ID__', pivotId);
    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}    // 🚀 Init
document.addEventListener('DOMContentLoaded', function() {
    // Main form: level → class → subject checkboxes
    document.getElementById('level_filter').addEventListener('change', function() {
        filterClassesByLevel('level_filter', 'class_id', null);
    });
    document.getElementById('class_id').addEventListener('change', function() {
        filterSubjectCheckboxesByClass('class_id', 'subjects_container');
    });

    // Edit modal: level → class → subject select
    document.getElementById('edit_level_filter').addEventListener('change', function() {
        filterClassesByLevel('edit_level_filter', 'edit_class_id', 'edit_subject_id');
    });
    document.getElementById('edit_class_id').addEventListener('change', function() {
        filterSubjectsByClass('edit_class_id', 'edit_subject_id');
    });

    // Initial filter state
    filterClassesByLevel('level_filter', 'class_id', null);
});
</script>
@endsection
