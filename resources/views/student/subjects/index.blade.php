@extends('layouts.student')

@section('title', 'Mes matières')
@section('page_title', 'Mes matières')
@section('breadcrumb', 'Matière → Niveau → Classe → Créneau')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/student-subjects-v5.css') }}">
<style>
.student-path-slot {display:inline-flex;align-items:center;justify-content:center;min-width:40px;padding:5px 10px;border-radius:9px;background:rgba(124,58,237,.12);border:1px solid rgba(139,92,246,.18);color:#ddd6fe;font-weight:800;font-size:.72rem}
.student-four-filter {display:grid;grid-template-columns:repeat(4,minmax(0,1fr)) auto;gap:12px;align-items:end}
@media(max-width:1050px){.student-four-filter{grid-template-columns:repeat(2,minmax(0,1fr))}.student-four-filter .learning-reset-button{grid-column:1/-1}}
@media(max-width:650px){.student-four-filter{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="student-learning-page">
    <section class="learning-page-intro">
        <div class="learning-page-intro-copy">
            <span class="learning-section-kicker"><i class="bi bi-diagram-3-fill"></i> Mon parcours exact</span>
            <h2>Mes cours par créneau</h2>
            <p>Chaque accès suit maintenant Matière → Niveau → Classe → Créneau.</p>
        </div>
        <div class="learning-intro-stats">
            <div class="learning-intro-stat"><span><i class="bi bi-journal-bookmark-fill"></i></span><div><strong>{{ $visibleAssignments->pluck('subject_id')->unique()->count() }}</strong><small>matières</small></div></div>
            <div class="learning-intro-stat"><span><i class="bi bi-clock-history"></i></span><div><strong>{{ $visibleAssignments->count() }}</strong><small>groupes</small></div></div>
        </div>
    </section>

    @if($assignments->isNotEmpty())
    <section class="learning-filter-panel">
        <div class="learning-panel-heading">
            <div class="learning-panel-icon"><i class="bi bi-funnel-fill"></i></div>
            <div><h3>Filtrer mon parcours</h3><p>Matière → Niveau → Classe → Créneau.</p></div>
        </div>
        <form method="GET" action="{{ route('student.subjects.index') }}" class="student-four-filter">
            <div class="learning-field"><label>Matière</label><div class="learning-select-wrap"><i class="bi bi-journal-bookmark-fill"></i><select name="subject_id" id="subjectPathSubject"><option value="">Toutes les matières</option>@foreach($subjects as $subject)<option value="{{ $subject['id'] }}" {{ (string)$selectedSubjectId === (string)$subject['id'] ? 'selected' : '' }}>{{ $subject['name'] }}</option>@endforeach</select></div></div>
            <div class="learning-field"><label>Niveau</label><div class="learning-select-wrap"><i class="bi bi-layers-fill"></i><select name="level_id" id="subjectPathLevel" disabled><option value="">Tous les niveaux</option></select></div></div>
            <div class="learning-field"><label>Classe</label><div class="learning-select-wrap"><i class="bi bi-building-fill"></i><select name="class_id" id="subjectPathClass" disabled><option value="">Toutes les classes</option></select></div></div>
            <div class="learning-field"><label>Créneau</label><div class="learning-select-wrap"><i class="bi bi-clock-fill"></i><select name="class_slot_id" id="subjectPathSlot" disabled><option value="">Tous les créneaux</option></select></div></div>
            <div style="display:flex;gap:8px"><button type="submit" class="learning-primary-button" style="border:0"><i class="bi bi-search"></i> Afficher</button><a href="{{ route('student.subjects.index') }}" class="learning-reset-button"><i class="bi bi-arrow-counterclockwise"></i></a></div>
        </form>
    </section>
    @endif

    @if($visibleAssignments->isNotEmpty())
        <section>
            <div class="learning-list-heading"><div><span class="learning-section-kicker">Groupes disponibles</span><h3>Mes matières</h3></div><span class="learning-list-count">{{ $visibleAssignments->count() }} résultat{{ $visibleAssignments->count() > 1 ? 's' : '' }}</span></div>
            <div class="learning-subject-grid">
                @foreach($visibleAssignments as $assignment)
                    @php
                        $subjectName = $assignment->subject->name;
                        $slug = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($subjectName));
                        $tone = str_contains($slug,'coran') ? 'emerald' : (str_contains($slug,'arabe') ? 'indigo' : (str_contains($slug,'soutien') ? 'amber' : 'violet'));
                        $icon = str_contains($slug,'coran') ? 'book-half' : (str_contains($slug,'arabe') ? 'translate' : 'mortarboard-fill');
                    @endphp
                    <a href="{{ route('student.subjects.courses', [$assignment->subject_id,$assignment->level_id,$assignment->class_id]) }}?class_slot_id={{ $assignment->class_slot_id }}" class="learning-subject-card {{ $tone }}">
                        <div class="learning-subject-card-top"><span class="learning-subject-icon"><i class="bi bi-{{ $icon }}"></i></span><span class="student-path-slot">{{ $assignment->slot_code }}</span></div>
                        <div class="learning-subject-card-body"><span class="learning-subject-label">Matière</span><h4>{{ $subjectName }}</h4><div class="learning-subject-path"><span><i class="bi bi-mortarboard"></i>{{ $assignment->level->name }}</span><i class="bi bi-chevron-right"></i><span><i class="bi bi-building"></i>{{ $assignment->classRoom->name }}</span><i class="bi bi-chevron-right"></i><span><i class="bi bi-clock"></i>{{ $assignment->slot_code }}</span></div></div>
                        <div class="learning-subject-card-footer"><span>Consulter les cours de {{ $assignment->slot_code }}</span><i class="bi bi-arrow-right"></i></div>
                    </a>
                @endforeach
            </div>
        </section>
    @elseif($assignments->isNotEmpty())
        <section class="learning-empty-state"><span class="learning-empty-icon"><i class="bi bi-funnel"></i></span><h3>Aucun résultat</h3><p>Modifiez le parcours sélectionné.</p><a href="{{ route('student.subjects.index') }}" class="learning-primary-button">Tout afficher</a></section>
    @else
        <section class="learning-empty-state"><span class="learning-empty-icon"><i class="bi bi-clock-history"></i></span><h3>Aucun créneau assigné</h3><p>L’administration doit vous affecter à un créneau D1/D2/I1/A1…</p></section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subject = document.getElementById('subjectPathSubject');
    const level = document.getElementById('subjectPathLevel');
    const classroom = document.getElementById('subjectPathClass');
    const slot = document.getElementById('subjectPathSlot');

    if (!subject || !level || !classroom || !slot) return;

    const levelsBySubject = @json($levelsBySubject);
    const classesBySubjectLevel = @json($classesBySubjectLevel);
    const slotsByPath = @json($slotsByPath);

    const selectedLevelId = @json((string) ($selectedLevelId ?? ''));
    const selectedClassId = @json((string) ($selectedClassId ?? ''));
    const selectedSlotId = @json((string) ($selectedSlotId ?? ''));

    function addOption(select, value, label, selected = false) {
        const option = document.createElement('option');
        option.value = String(value);
        option.textContent = label;
        option.selected = selected;
        select.appendChild(option);
    }

    function fillSlots(wanted = '') {
        slot.innerHTML = '';
        addOption(slot, '', 'Tous les créneaux');

        const options = (((slotsByPath[String(subject.value)] || {})[String(level.value)] || {})[String(classroom.value)] || []);
        options.forEach(item => addOption(
            slot,
            item.id,
            item.code,
            String(item.id) === String(wanted)
        ));
        slot.disabled = !subject.value || !level.value || !classroom.value;
    }

    function fillClasses(wanted = '', wantedSlot = '') {
        classroom.innerHTML = '';
        addOption(classroom, '', 'Toutes les classes');
        const options = ((classesBySubjectLevel[String(subject.value)] || {})[String(level.value)] || []);
        options.forEach(item => addOption(
            classroom,
            item.id,
            item.name,
            String(item.id) === String(wanted)
        ));
        classroom.disabled = !subject.value || !level.value;
        fillSlots(wantedSlot);
    }

    function fillLevels(wanted = '', wantedClass = '', wantedSlot = '') {
        level.innerHTML = '';
        addOption(level, '', 'Tous les niveaux');
        const options = levelsBySubject[String(subject.value)] || [];
        options.forEach(item => addOption(
            level,
            item.id,
            item.name,
            String(item.id) === String(wanted)
        ));
        level.disabled = !subject.value;
        fillClasses(wantedClass, wantedSlot);
    }

    subject.addEventListener('change', () => fillLevels());
    level.addEventListener('change', () => fillClasses());
    classroom.addEventListener('change', () => fillSlots());

    fillLevels(selectedLevelId, selectedClassId, selectedSlotId);
});
</script>
@endpush
