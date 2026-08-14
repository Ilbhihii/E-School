@extends('layouts.parent')
@section('title', $student->name)
@section('page_title', 'Suivi — ' . $student->name)

@section('content')
<div class="parent-hero"><div><h2>{{ $student->name }}</h2><p>Vue générale du dossier scolaire.</p></div></div>
@include('parent.children._tabs', ['student' => $student])

<div class="parent-stats">
    <div><strong>{{ $summary->presence_count }}</strong><span>Présences</span></div>
    <div><strong>{{ $summary->absence_count }}</strong><span>Absences</span></div>
    <div><strong>{{ $summary->pending_assignments }}</strong><span>Devoirs sans note</span></div>
    <div><strong>{{ $summary->results_count }}</strong><span>Résultats</span></div>
</div>

<h3 class="parent-section-title">Parcours</h3>
<div class="parent-card parent-paths">
@forelse($paths as $path)
    <span>{{ $path->subject_name ?? 'Matière' }} → {{ $path->level_name ?? 'Niveau' }} → {{ $path->class_name ?? 'Classe' }} @if($path->slot_code) → {{ $path->slot_code }} @endif</span>
@empty
    <span>Aucun parcours assigné</span>
@endforelse
</div>

@if((bool) $link->can_view_absences)
<h3 class="parent-section-title">Dernières présences</h3>
<div class="parent-table-wrap">
<table class="parent-table"><thead><tr><th>Date</th><th>Matière</th><th>Classe</th><th>État</th></tr></thead><tbody>
@forelse($recentAbsences as $absence)
<tr><td>{{ optional($absence->date)->format('d/m/Y') }}</td><td>{{ optional($absence->subject)->name ?? '—' }}</td><td>{{ optional($absence->classRoom)->name ?? '—' }}</td><td>{{ $absence->present ? 'Présent' : 'Absent' }}</td></tr>
@empty
<tr><td colspan="4">Aucun enregistrement.</td></tr>
@endforelse
</tbody></table>
</div>
@endif

@if((bool) $link->can_view_assignments)
<h3 class="parent-section-title">Derniers devoirs</h3>
<div class="parent-table-wrap">
<table class="parent-table"><thead><tr><th>Devoir</th><th>Matière</th><th>Échéance</th><th>Note</th></tr></thead><tbody>
@forelse($recentAssignments as $assignment)
<tr><td>{{ $assignment->title }}</td><td>{{ optional($assignment->subject)->name ?? '—' }}</td><td>{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') : '—' }}</td><td>{{ $assignment->grade !== null ? $assignment->grade : 'En attente' }}</td></tr>
@empty
<tr><td colspan="4">Aucun devoir.</td></tr>
@endforelse
</tbody></table>
</div>
@endif
@endsection
