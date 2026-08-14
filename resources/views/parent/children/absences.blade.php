@extends('layouts.parent')
@section('title', 'Présences')
@section('page_title', 'Présences — ' . $student->name)
@section('content')
<div class="parent-hero"><div><h2>Présences & absences</h2><p>Historique de {{ $student->name }}.</p></div></div>
@include('parent.children._tabs', ['student' => $student])
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Date</th><th>Matière</th><th>Niveau</th><th>Classe</th><th>Créneau</th><th>État</th></tr></thead><tbody>
@forelse($absences as $absence)
<tr><td>{{ optional($absence->date)->format('d/m/Y') }}</td><td>{{ optional($absence->subject)->name ?? '—' }}</td><td>{{ optional($absence->level)->name ?? '—' }}</td><td>{{ optional($absence->classRoom)->name ?? '—' }}</td><td>{{ optional($absence->classSlot)->code ?? '—' }}</td><td>{{ $absence->present ? 'Présent' : 'Absent' }}</td></tr>
@empty
<tr><td colspan="6">Aucun enregistrement.</td></tr>
@endforelse
</tbody></table></div>
<div class="parent-pagination">{{ $absences->links() }}</div>
@endsection
