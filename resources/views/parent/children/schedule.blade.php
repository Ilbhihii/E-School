@extends('layouts.parent')
@section('title', 'Emploi du temps')
@section('page_title', 'Emploi du temps — ' . $student->name)
@section('content')
<div class="parent-hero"><div><h2>Emploi du temps</h2><p>Planning des créneaux de {{ $student->name }}.</p></div></div>
@include('parent.children._tabs', ['student' => $student])
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Jour</th><th>Horaire</th><th>Matière</th><th>Niveau</th><th>Classe</th><th>Créneau</th><th>Professeur</th></tr></thead><tbody>
@forelse($schedules as $schedule)
<tr><td>{{ $schedule->day_label ?? '—' }}</td><td>{{ optional($schedule->start_time)->format('H:i') }} — {{ optional($schedule->end_time)->format('H:i') }}</td><td>{{ optional($schedule->subjectModel)->name ?? $schedule->subject ?? '—' }}</td><td>{{ optional($schedule->level)->name ?? '—' }}</td><td>{{ optional($schedule->classRoom)->name ?? '—' }}</td><td>{{ $schedule->slot_code ?? '—' }}</td><td>{{ optional($schedule->prof)->name ?? 'À définir' }}</td></tr>
@empty
<tr><td colspan="7">Aucun cours planifié.</td></tr>
@endforelse
</tbody></table></div>
@endsection
