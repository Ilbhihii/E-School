@extends('layouts.admin')

@section('title', 'Créer un live')
@section('page_title', 'Nouveau live')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau → Live')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        @if($errors->any())
        <div class="adm-alert adm-alert-danger mb-4">
            <span class="adm-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- MEETING PROVIDER FIRST -->
        <div class="adm-card" style="border-left:4px solid #38BDF8;margin-bottom:1.5rem;">
            <div class="adm-card-header" style="background:linear-gradient(135deg, rgba(2,132,199,0.08), rgba(14,165,233,0.03));">
                <h4><i class="bi bi-camera-video" style="color:#38BDF8;"></i> Créer une réunion en ligne</h4>
                <span style="color:#64748B;font-size:0.75rem;">Recommandé</span>
            </div>
            <div class="adm-card-body">
                <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
                    <div style="flex:1;min-width:240px;">
                        <p style="color:#94A3B8;font-size:0.85rem;margin-bottom:1rem;">
                            Choisissez Microsoft Teams ou Google Meet, créez la réunion, puis collez son lien.
                            Les informations seront automatiquement reprises dans le live Laravel.
                        </p>

                        <div class="adm-form-group" style="margin-bottom:1rem;">
                            <label class="adm-form-label" style="font-size:0.75rem;">Plateforme</label>
                            <select id="meeting_provider" class="adm-form-select" style="font-size:0.85rem;">
                                <option value="teams" @selected(old('provider') === 'teams')>Microsoft Teams</option>
                                <option value="google_meet" @selected(old('provider', 'google_meet') === 'google_meet')>Google Meet</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Titre</label>
                                    <input type="text" id="outlook_title" class="adm-form-control" placeholder="Titre du live" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Matière</label>
                                    <select id="outlook_subject_id" class="adm-form-select" style="font-size:0.85rem;">
                                        <option value="">Choisir une matière...</option>
                                        @foreach($subjects as $subject)
                                            <option
                                                value="{{ $subject->id }}"
                                                {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}
                                            >
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Niveau</label>
                                    <select
                                        id="outlook_level_id"
                                        class="adm-form-select"
                                        style="font-size:0.85rem;"
                                        disabled
                                    >
                                        <option value="">
                                            D'abord choisir une matière
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Classe</label>
                                    <select
                                        id="outlook_class_id"
                                        class="adm-form-select"
                                        style="font-size:0.85rem;"
                                        disabled
                                    >
                                        <option value="">
                                            D'abord choisir un niveau
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">
                                        Créneau / groupe
                                    </label>
                                    <select
                                        id="outlook_class_slot_id"
                                        class="adm-form-select"
                                        style="font-size:0.85rem;"
                                        disabled
                                    >
                                        <option value="">
                                            D'abord choisir une classe
                                        </option>
                                    </select>
                                    <small style="display:block;margin-top:6px;color:#64748B;font-size:0.7rem;">
                                        Débutant → D1/D2/D3/D4 · Intermédiaire → I1/I2/I3/I4 · Avancé → A1/A2/A3/A4.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Date</label>
                                    <input type="date" id="outlook_date" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Début</label>
                                    <input type="time" id="outlook_start" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Fin</label>
                                    <input type="time" id="outlook_end" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                        </div>

                        <div class="adm-form-group mt-2" style="margin-bottom:0;">
                            <label class="adm-form-label" style="font-size:0.75rem;">
                                Lien de la réunion 
                                <span style="color:#EF4444;font-size:0.7rem;font-weight:400;">(obligatoire — à coller après création)</span>
                            </label>
                            <div style="display:flex;gap:6px;flex-direction:column;">
                                <input type="url" id="outlook_url" class="adm-form-control" placeholder="https://meet.google.com/xxx-xxxx-xxx" style="font-size:0.85rem;flex:1;">
                                <div style="font-size:0.7rem;color:#64748B;display:flex;align-items:center;gap:6px;">
                                    <i class="bi bi-info-circle"></i>
                                    Créez la réunion sur la plateforme choisie, copiez son lien puis collez-le ici
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:0.75rem;padding:2rem 1.5rem;border-left:1px solid rgba(255,255,255,0.06);min-width:200px;">
                        <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,#0284C7,#0EA5E9);display:flex;align-items:center;justify-content:center;font-size:2rem;color:white;box-shadow:0 8px 24px rgba(2,132,199,0.3);">
                            <i class="bi bi-calendar-plus-fill"></i>
                        </div>
                        <a href="#" id="outlookMainBtn" target="_blank"
                           style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:12px;background:linear-gradient(135deg,#0284C7,#0EA5E9);color:white;font-weight:700;font-size:0.9rem;text-decoration:none;transition:all 0.2s;pointer-events:none;opacity:0.4;white-space:nowrap;box-shadow:0 4px 16px rgba(2,132,199,0.2);"
                           onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(2,132,199,0.35)'"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(2,132,199,0.2)'">
                            <i class="bi bi-plus-circle" style="font-size:1.1rem;"></i>
                            Créer la réunion
                        </a>
                        <span id="outlookStatus" style="font-size:0.7rem;color:#64748B;text-align:center;">Remplissez les champs (titre, date, heure, classe)</span>
                        <button type="button" id="saveLiveBtn" class="adm-btn adm-btn-success" style="width:100%;pointer-events:none;opacity:0.4;" onclick="saveLive()">
                            <i class="bi bi-save me-1"></i> Enregistrer le live
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MANUAL FORM (SECONDARY) -->
        <details style="margin-bottom:1.5rem;">
            <summary style="cursor:pointer;padding:0.75rem 1rem;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:#64748B;font-size:0.82rem;font-weight:500;transition:all 0.2s;"
                     onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                <i class="bi bi-chevron-down me-2"></i> Création manuelle (optionnel)
            </summary>
            <div class="adm-card mt-3">
                <div class="adm-card-header">
                    <h4><i class="bi bi-pencil-square" style="color:rgba(255,255,255,0.35);"></i> Formulaire manuel</h4>
                </div>
                <div class="adm-card-body">
                    @if(session('success'))
                    <div class="adm-alert adm-alert-success mb-4">
                        <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.lives.store') }}" id="manualForm">
                        @csrf
                        <input type="hidden" name="provider" value="{{ old('provider', 'google_meet') }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Titre du live</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="adm-form-control @error('title') error @enderror" placeholder="Ex: Révision Math" required>
                                    @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Matière</label>
                                    <select
                                        name="subject_id"
                                        id="manual_subject_id"
                                        class="adm-form-select
                                            @error('subject_id')
                                                error
                                            @enderror"
                                        required
                                    >
                                        <option value="">Choisir une matière...</option>
                                        @foreach($subjects as $subject)
                                            <option
                                                value="{{ $subject->id }}"
                                                {{ (string) old('subject_id') === (string) $subject->id ? 'selected' : '' }}
                                            >
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('subject_id')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Niveau</label>
                                    <select
                                        name="level_id"
                                        id="manual_level_id"
                                        class="adm-form-select
                                            @error('level_id')
                                                error
                                            @enderror"
                                        disabled
                                        required
                                    >
                                        <option value="">
                                            D'abord choisir une matière
                                        </option>
                                    </select>

                                    @error('level_id')
                                        <div class="adm-form-error">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Classe</label>
                                    <select
                                        name="class_id"
                                        id="manual_class_id"
                                        class="adm-form-select
                                            @error('class_id')
                                                error
                                            @enderror"
                                        disabled
                                        required
                                    >
                                        <option value="">
                                            D'abord choisir un niveau
                                        </option>
                                    </select>
                                    @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">
                                Créneau / groupe
                            </label>
                            <select
                                name="class_slot_id"
                                id="manual_class_slot_id"
                                class="adm-form-select @error('class_slot_id') error @enderror"
                                disabled
                                required
                            >
                                <option value="">
                                    D'abord choisir une classe
                                </option>
                            </select>
                            <small style="display:block;margin-top:6px;color:#64748B;font-size:0.7rem;">
                                Créneau structurel indépendant de l'emploi du temps.
                            </small>
                            @error('class_slot_id')
                                <div class="adm-form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Lien de la réunion <span style="color:#EF4444;font-size:0.7rem;font-weight:400;">(obligatoire)</span></label>
                            <input type="url" name="stream_url" value="{{ old('stream_url') }}" class="adm-form-control @error('stream_url') error @enderror" placeholder="https://meet.google.com/xxx-xxxx-xxx">
                            <div style="font-size:0.7rem;color:#64748B;margin-top:0.35rem;">
                                <i class="bi bi-info-circle"></i>
                                Utilisez un lien Google Meet ou Microsoft Teams correspondant à la plateforme choisie ci-dessus
                            </div>
                            @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Date & heure</label>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <input
                                        type="date"
                                        name="live_date"
                                        value="{{ old('live_date') }}"
                                        class="adm-form-control"
                                        required
                                    >
                                </div>
                                <div class="col-md-4">
                                    <input
                                        type="time"
                                        name="start_time"
                                        value="{{ old('start_time') }}"
                                        class="adm-form-control"
                                        required
                                    >
                                </div>
                                <div class="col-md-4">
                                    <input
                                        type="time"
                                        name="end_time"
                                        value="{{ old('end_time') }}"
                                        class="adm-form-control"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="adm-btn adm-btn-ghost w-100 mt-3" style="border:1px solid rgba(255,255,255,0.08);">
                            <i class="bi bi-save me-2"></i> Enregistrer dans Laravel
                        </button>
                    </form>
                </div>
            </div>
        </details>

        <!-- RECENT LIVES -->
        <div class="adm-card mt-4" style="border-left:4px solid #22C55E;">
            <div class="adm-card-header" style="background:linear-gradient(135deg, rgba(34,197,94,0.08), rgba(34,197,94,0.03));">
                <h4><i class="bi bi-check2-circle" style="color:#22C55E;"></i> Lives enregistrés</h4>
                <div class="card-actions">
                    <span style="color:var(--adm-text-muted);font-size:0.78rem;">{{ $recentLives->count() }} live(s) récents</span>
                </div>
            </div>
            <div class="adm-card-body p-0">
                @if($recentLives->isEmpty())
                    <div class="adm-empty" style="padding:3rem 2rem;">
                        <div class="adm-empty-icon"><i class="bi bi-camera-video"></i></div>
                        <h5>Aucun live enregistré</h5>
                        <p>Les lives que vous créerez apparaîtront ici avec leur date, heure et lien.</p>
                    </div>
                @else
                    <div class="adm-table-wrap">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Parcours</th>
                                    <th>Date</th>
                                    <th>Horaire</th>
                                    <th>Lien de réunion</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLives as $live)
                                <tr>
                                    <td><span style="font-weight:500;">{{ $live->title }}</span></td>
                                    <td>
                                        @if($live->classSlot)
                                            <div style="display:flex;flex-wrap:wrap;gap:5px;align-items:center;">
                                                <span class="adm-badge adm-badge-primary">{{ $live->classSlot->subject?->name ?? '—' }}</span>
                                                <span style="color:#64748B;">→</span>
                                                <span class="adm-badge">{{ $live->classSlot->level?->name ?? '—' }}</span>
                                                <span style="color:#64748B;">→</span>
                                                <span class="adm-badge adm-badge-danger">{{ $live->classSlot->classRoom?->name ?? '—' }}</span>
                                                <span style="color:#64748B;">→</span>
                                                <span class="adm-badge adm-badge-warning">{{ $live->classSlot->code }}</span>
                                            </div>
                                        @elseif($live->classRoom)
                                            <span class="adm-badge adm-badge-danger">{{ $live->classRoom->name }}</span>
                                            <small style="display:block;color:#64748B;margin-top:4px;">Ancien live sans créneau</small>
                                        @else
                                            <span style="color:var(--adm-text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                        @if($live->live_date)
                                            {{ \Carbon\Carbon::parse($live->live_date)->format('d/m/Y') }}
                                        @else
                                            {{ $live->created_at->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td style="color:var(--adm-text-muted);font-size:0.85rem;">
                                        @if($live->start_time)
                                            {{ $live->start_time }} @if($live->end_time) — {{ $live->end_time }} @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($live->stream_url)
                                        <a href="{{ $live->stream_url }}" target="_blank" class="adm-btn adm-btn-ghost adm-btn-sm" title="{{ $live->stream_url }}" style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-flex;align-items:center;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>
                                            <span style="overflow:hidden;text-overflow:ellipsis;">{{ $live->stream_url }}</span>
                                        </a>
                                        @else
                                        <span style="color:var(--adm-text-muted);font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                                            <a href="{{ route('admin.lives.edit', $live) }}" class="adm-btn adm-btn-warning adm-btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.lives.destroy',$live) }}" style="display:inline;" onsubmit="return confirm('Supprimer ce live ?')">
                                                @csrf @method('DELETE')
                                                <button class="adm-btn adm-btn-danger adm-btn-sm" type="submit">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const hierarchy = @json($liveHierarchy);

    const initialSubjectId = @json(
        (string) old('subject_id', '')
    );

    const initialLevelId = @json(
        (string) old('level_id', '')
    );

    const initialClassId = @json(
        (string) old('class_id', '')
    );

    const initialClassSlotId = @json(
        (string) old('class_slot_id', '')
    );

    const prefixes = [
        'outlook',
        'manual',
    ];

    const findSubject = subjectId =>
        hierarchy.find(
            subject =>
                String(subject.id)
                === String(subjectId)
        );

    const findLevel = (
        subject,
        levelId
    ) => {
        if (!subject) {
            return null;
        }

        return subject.levels.find(
            level =>
                String(level.id)
                === String(levelId)
        );
    };

    const findClass = (
        subject,
        level,
        classId
    ) => {
        if (!subject || !level) {
            return null;
        }

        return level.classes.find(
            classRoom =>
                String(classRoom.id)
                === String(classId)
        );
    };

    const createOption = (
        value,
        label,
        selectedValue = ''
    ) => {
        const option =
            document.createElement('option');

        option.value = String(value);
        option.textContent = label;
        option.selected =
            String(value)
            === String(selectedValue);

        return option;
    };

    const resetSelect = (
        select,
        placeholder,
        disabled = true
    ) => {
        if (!select) {
            return;
        }

        select.innerHTML = '';

        select.appendChild(
            createOption(
                '',
                placeholder
            )
        );

        select.disabled = disabled;
        select.value = '';
    };

    const populateSlots = (
        prefix,
        subject,
        levelId,
        classId,
        selectedSlotId = ''
    ) => {
        const slotSelect =
            document.getElementById(
                `${prefix}_class_slot_id`
            );

        const level = findLevel(subject, levelId);
        const classRoom = findClass(
            subject,
            level,
            classId
        );

        resetSelect(
            slotSelect,
            classRoom
                ? 'Choisir un créneau...'
                : 'D’abord choisir une classe',
            !classRoom
        );

        if (!classRoom || !slotSelect) {
            return;
        }

        (classRoom.slots || []).forEach(slot => {
            slotSelect.appendChild(
                createOption(
                    slot.id,
                    slot.code,
                    selectedSlotId
                )
            );
        });

        slotSelect.disabled = false;

        if (selectedSlotId) {
            slotSelect.value = String(selectedSlotId);
        }
    };

    const populateClasses = (
        prefix,
        subject,
        levelId,
        selectedClassId = '',
        selectedSlotId = ''
    ) => {
        const classSelect =
            document.getElementById(
                `${prefix}_class_id`
            );

        const slotSelect =
            document.getElementById(
                `${prefix}_class_slot_id`
            );

        const level = findLevel(
            subject,
            levelId
        );

        resetSelect(
            classSelect,
            level
                ? 'Choisir une classe...'
                : 'D’abord choisir un niveau',
            !level
        );

        resetSelect(
            slotSelect,
            'D’abord choisir une classe',
            true
        );

        if (!level || !classSelect) {
            return;
        }

        level.classes.forEach(classRoom => {
            classSelect.appendChild(
                createOption(
                    classRoom.id,
                    classRoom.name,
                    selectedClassId
                )
            );
        });

        classSelect.disabled = false;

        if (selectedClassId) {
            classSelect.value =
                String(selectedClassId);

            populateSlots(
                prefix,
                subject,
                levelId,
                selectedClassId,
                selectedSlotId
            );
        }
    };

    const populateLevels = (
        prefix,
        subjectId,
        selectedLevelId = '',
        selectedClassId = '',
        selectedSlotId = ''
    ) => {
        const levelSelect =
            document.getElementById(
                `${prefix}_level_id`
            );

        const classSelect =
            document.getElementById(
                `${prefix}_class_id`
            );

        const slotSelect =
            document.getElementById(
                `${prefix}_class_slot_id`
            );

        const subject = findSubject(
            subjectId
        );

        resetSelect(
            levelSelect,
            subject
                ? 'Choisir un niveau...'
                : 'D’abord choisir une matière',
            !subject
        );

        resetSelect(
            classSelect,
            'D’abord choisir un niveau',
            true
        );

        resetSelect(
            slotSelect,
            'D’abord choisir une classe',
            true
        );

        if (!subject || !levelSelect) {
            return;
        }

        subject.levels.forEach(level => {
            levelSelect.appendChild(
                createOption(
                    level.id,
                    level.name,
                    selectedLevelId
                )
            );
        });

        levelSelect.disabled = false;

        if (selectedLevelId) {
            levelSelect.value =
                String(selectedLevelId);

            populateClasses(
                prefix,
                subject,
                selectedLevelId,
                selectedClassId,
                selectedSlotId
            );
        }
    };

    const copyMainToManual = () => {
        const manualSubject =
            document.getElementById(
                'manual_subject_id'
            );

        const outlookSubject =
            document.getElementById(
                'outlook_subject_id'
            );

        const outlookLevel =
            document.getElementById(
                'outlook_level_id'
            );

        const outlookClass =
            document.getElementById(
                'outlook_class_id'
            );

        const outlookSlot =
            document.getElementById(
                'outlook_class_slot_id'
            );

        if (
            manualSubject
            && outlookSubject
        ) {
            manualSubject.value =
                outlookSubject.value;

            populateLevels(
                'manual',
                outlookSubject.value,
                outlookLevel?.value ?? '',
                outlookClass?.value ?? '',
                outlookSlot?.value ?? ''
            );
        }
    };

    const syncFields = () => {
        const form =
            document.getElementById(
                'manualForm'
            );

        if (!form) {
            return;
        }

        const setField = (
            selector,
            value
        ) => {
            const field =
                form.querySelector(selector);

            if (field) {
                field.value = value ?? '';
            }
        };

        setField(
            '[name="title"]',
            document.getElementById(
                'outlook_title'
            )?.value
        );

        setField(
            '[name="live_date"]',
            document.getElementById(
                'outlook_date'
            )?.value
        );

        setField(
            '[name="start_time"]',
            document.getElementById(
                'outlook_start'
            )?.value
        );

        setField(
            '[name="end_time"]',
            document.getElementById(
                'outlook_end'
            )?.value
        );

        setField(
            '[name="stream_url"]',
            document.getElementById(
                'outlook_url'
            )?.value
        );

        setField(
            '[name="provider"]',
            document.getElementById(
                'meeting_provider'
            )?.value ?? 'google_meet'
        );

        copyMainToManual();
        updateProviderButtons();
    };

    const updateProviderButtons = () => {
        const provider =
            document.getElementById(
                'meeting_provider'
            )?.value ?? 'google_meet';

        const streamUrl =
            document.getElementById(
                'outlook_url'
            )?.value ?? '';

        const hasDetails = Boolean(
            document.getElementById(
                'outlook_title'
            )?.value
            && document.getElementById(
                'outlook_subject_id'
            )?.value
            && document.getElementById(
                'outlook_level_id'
            )?.value
            && document.getElementById(
                'outlook_class_id'
            )?.value
            && document.getElementById(
                'outlook_class_slot_id'
            )?.value
            && document.getElementById(
                'outlook_date'
            )?.value
            && document.getElementById(
                'outlook_start'
            )?.value
            && document.getElementById(
                'outlook_end'
            )?.value
        );

        const config =
            provider === 'teams'
                ? {
                    name: 'Microsoft Teams',
                    url: 'https://teams.microsoft.com/',
                    host:
                        /(^|\.)teams\.(microsoft|live)\.com$/i,
                    color: '#6264A7',
                }
                : {
                    name: 'Google Meet',
                    url: 'https://meet.google.com/',
                    host: /^meet\.google\.com$/i,
                    color: '#0F9D58',
                };

        const createButton =
            document.getElementById(
                'outlookMainBtn'
            );

        const saveButton =
            document.getElementById(
                'saveLiveBtn'
            );

        const status =
            document.getElementById(
                'outlookStatus'
            );

        if (!createButton) {
            return;
        }

        createButton.href = config.url;
        createButton.innerHTML =
            '<i class="bi bi-camera-video me-1"></i>'
            + ' Ouvrir '
            + config.name;

        createButton.style.background =
            config.color;

        createButton.style.pointerEvents =
            hasDetails
                ? 'auto'
                : 'none';

        createButton.style.opacity =
            hasDetails
                ? '1'
                : '0.4';

        let validMeetingUrl = false;

        try {
            validMeetingUrl =
                config.host.test(
                    new URL(streamUrl).hostname
                );
        } catch (error) {
            validMeetingUrl = false;
        }

        const canSave =
            hasDetails
            && validMeetingUrl;

        if (saveButton) {
            saveButton.style.pointerEvents =
                canSave
                    ? 'auto'
                    : 'none';

            saveButton.style.opacity =
                canSave
                    ? '1'
                    : '0.4';
        }

        if (status) {
            status.innerHTML =
                hasDetails
                    ? '<span style="color:#34D399;">'
                        + '<i class="bi bi-check-circle me-1"></i>'
                        + 'Créez la réunion puis collez son lien'
                        + '</span>'
                    : 'Remplissez Matière → Niveau → Classe → Créneau, '
                        + 'titre, date et heures';
        }
    };

    prefixes.forEach(prefix => {
        const subjectSelect =
            document.getElementById(
                `${prefix}_subject_id`
            );

        const levelSelect =
            document.getElementById(
                `${prefix}_level_id`
            );

        const classSelect =
            document.getElementById(
                `${prefix}_class_id`
            );

        const slotSelect =
            document.getElementById(
                `${prefix}_class_slot_id`
            );

        subjectSelect?.addEventListener(
            'change',
            () => {
                populateLevels(
                    prefix,
                    subjectSelect.value
                );

                if (prefix === 'outlook') {
                    syncFields();
                }
            }
        );

        levelSelect?.addEventListener(
            'change',
            () => {
                populateClasses(
                    prefix,
                    findSubject(
                        subjectSelect?.value
                    ),
                    levelSelect.value
                );

                if (prefix === 'outlook') {
                    syncFields();
                }
            }
        );

        classSelect?.addEventListener(
            'change',
            () => {
                populateSlots(
                    prefix,
                    findSubject(subjectSelect?.value),
                    levelSelect?.value ?? '',
                    classSelect.value
                );

                if (prefix === 'outlook') {
                    syncFields();
                }
            }
        );

        slotSelect?.addEventListener(
            'change',
            () => {
                if (prefix === 'outlook') {
                    syncFields();
                }
            }
        );
    });

    [
        'meeting_provider',
        'outlook_title',
        'outlook_date',
        'outlook_start',
        'outlook_end',
        'outlook_url',
    ].forEach(id => {
        const element =
            document.getElementById(id);

        element?.addEventListener(
            'input',
            syncFields
        );

        element?.addEventListener(
            'change',
            syncFields
        );
    });

    /*
     * Restaurer les anciennes valeurs après validation.
     */
    prefixes.forEach(prefix => {
        const subjectSelect =
            document.getElementById(
                `${prefix}_subject_id`
            );

        if (
            initialSubjectId
            && subjectSelect
        ) {
            subjectSelect.value =
                String(initialSubjectId);

            populateLevels(
                prefix,
                initialSubjectId,
                initialLevelId,
                initialClassId,
                initialClassSlotId
            );
        } else {
            populateLevels(
                prefix,
                ''
            );
        }
    });

    updateProviderButtons();

    window.saveLive = () => {
        syncFields();

        document.getElementById(
            'manualForm'
        )?.requestSubmit();
    };
});
</script>

@endsection