@extends('layouts.student')
@section('title','Mes absences')
@section('page_title','Mes absences')
@section('breadcrumb','Matière → Niveau → Classe → Créneau → Absences')
@push('styles')<link rel="stylesheet" href="{{ asset('css/student-pages-v6.css') }}"><style>.absence-student-path{display:flex;flex-wrap:wrap;gap:5px;align-items:center}.absence-student-slot{display:inline-flex;padding:4px 9px;border-radius:8px;background:rgba(124,58,237,.12);border:1px solid rgba(139,92,246,.18);color:#ddd6fe;font-weight:800}</style>@endpush
@section('content')
@php $unjustifiedCount=$totalAbsences-($justifiedCount??0);$situationClass=match($color??'success'){'danger'=>'danger','warning'=>'warning',default=>'success'};$situationIcon=match($color??'success'){'danger'=>'exclamation-octagon-fill','warning'=>'exclamation-triangle-fill',default=>'check-circle-fill'}; @endphp
<div class="sp-page sp-absences-page">
<section class="sp-hero sp-hero-absence"><div class="sp-hero-icon"><i class="bi bi-calendar2-x-fill"></i></div><div class="sp-hero-copy"><span class="sp-kicker">Suivi de présence</span><h2>Mes absences</h2><p>Historique par Matière → Niveau → Classe → Créneau.</p></div><div class="sp-absence-situation {{ $situationClass }}"><i class="bi bi-{{ $situationIcon }}"></i><div><small>Situation actuelle</small><strong>{{ $situation }}</strong></div></div></section>
@if($paths->isNotEmpty())<section class="sp-filter-card"><div class="sp-card-heading"><div class="sp-card-heading-icon red"><i class="bi bi-funnel-fill"></i></div><div><h3>Filtrer mes absences</h3><p>Matière → Niveau → Classe → Créneau.</p></div>@if($hasActiveFilter)<a href="{{ route('student.absences') }}" class="sp-reset-link"><i class="bi bi-arrow-counterclockwise"></i>Tout afficher</a>@endif</div><form method="GET" action="{{ route('student.absences') }}" class="sp-filter-grid">
<div class="sp-field"><label>Matière</label><div class="sp-select-wrap"><i class="bi bi-journal-bookmark-fill"></i><select name="subject_id" id="absenceSubject"><option value="">Toutes les matières</option>@foreach($subjects as $item)<option value="{{ $item['id'] }}" {{ (string)$selectedSubjectId===(string)$item['id']?'selected':'' }}>{{ $item['name'] }}</option>@endforeach</select></div></div>
<div class="sp-field"><label>Niveau</label><div class="sp-select-wrap"><i class="bi bi-layers-fill"></i><select name="level_id" id="absenceLevel" disabled><option value="">Tous les niveaux</option></select></div></div>
<div class="sp-field"><label>Classe</label><div class="sp-select-wrap"><i class="bi bi-building-fill"></i><select name="class_id" id="absenceClass" disabled><option value="">Toutes les classes</option></select></div></div>
<div class="sp-field"><label>Créneau</label><div class="sp-select-wrap"><i class="bi bi-clock-fill"></i><select name="class_slot_id" id="absenceSlot" disabled><option value="">Tous les créneaux</option></select></div></div>
<button type="submit" class="sp-primary-button red"><i class="bi bi-search"></i>Afficher</button></form></section>@endif
<section class="sp-metrics sp-metrics-three"><article class="sp-metric-card"><span class="sp-metric-icon red"><i class="bi bi-x-circle-fill"></i></span><div><small>Total des absences</small><strong>{{ $totalAbsences }}</strong></div></article><article class="sp-metric-card"><span class="sp-metric-icon amber"><i class="bi bi-exclamation-triangle-fill"></i></span><div><small>Affichées</small><strong>{{ $absences->count() }}</strong></div></article><article class="sp-metric-card"><span class="sp-metric-icon green"><i class="bi bi-check-circle-fill"></i></span><div><small>Justifiées</small><strong>{{ $justifiedCount??0 }}</strong></div></article></section>
<section class="sp-table-card"><header class="sp-section-header"><div><span class="sp-section-icon red"><i class="bi bi-clock-history"></i></span><div><h3>Historique des absences</h3><p>Chaque absence est rattachée à votre groupe exact.</p></div></div><span class="sp-soft-badge">{{ $absences->count() }} entrée{{ $absences->count()>1?'s':'' }}</span></header>
@if($absences->isNotEmpty())<div class="sp-responsive-table"><table><thead><tr><th>Date</th><th>Parcours</th><th>Créneau</th><th>Statut</th></tr></thead><tbody>@foreach($absences as $absence)@php $d=\Carbon\Carbon::parse($absence->date); @endphp<tr><td data-label="Date"><strong>{{ $d->format('d/m/Y') }}</strong><small class="sp-date-sub">{{ ucfirst($d->translatedFormat('l')) }}</small></td><td data-label="Parcours"><div class="absence-student-path"><span>{{ $absence->subject?->name ?? 'Matière' }}</span><i class="bi bi-chevron-right"></i><span>{{ $absence->level?->name ?? 'Niveau' }}</span><i class="bi bi-chevron-right"></i><span>{{ $absence->classRoom?->name ?? 'Classe' }}</span></div></td><td data-label="Créneau">@if($absence->classSlot)<span class="absence-student-slot">{{ $absence->classSlot->code }}</span>@else<span class="sp-muted-value">Ancienne absence sans créneau</span>@endif</td><td data-label="Statut"><span class="sp-status-badge danger"><i class="bi bi-x-circle-fill"></i>Absent</span></td></tr>@endforeach</tbody></table></div>@else<div class="sp-empty-state"><span class="sp-empty-icon green"><i class="bi bi-calendar2-check-fill"></i></span><h3>Aucune absence</h3><p>Aucune absence pour le parcours sélectionné.</p></div>@endif</section>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const subject = document.getElementById('absenceSubject');
    const level = document.getElementById('absenceLevel');
    const classroom = document.getElementById('absenceClass');
    const slot = document.getElementById('absenceSlot');

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
