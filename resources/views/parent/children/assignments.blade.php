@extends('layouts.parent')
@section('title', 'Devoirs')
@section('page_title', 'Devoirs — ' . $student->name)
@section('content')
<div class="parent-hero"><div><h2>Devoirs</h2><p>Le parent consulte le suivi ; l'envoi reste effectué depuis le compte étudiant.</p></div></div>
@include('parent.children._tabs', ['student' => $student])
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Devoir</th><th>Matière</th><th>Créneau</th><th>Échéance</th><th>Note</th><th>Commentaire</th></tr></thead><tbody>
@forelse($assignments as $assignment)
<tr><td>{{ $assignment->title }}</td><td>{{ optional($assignment->subject)->name ?? '—' }}</td><td>{{ optional($assignment->classSlot)->code ?? '—' }}</td><td>{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') : '—' }}</td><td>{{ $assignment->grade !== null ? $assignment->grade : 'En attente' }}</td><td>{{ $assignment->comment ?: '—' }}</td></tr>
@empty
<tr><td colspan="6">Aucun devoir.</td></tr>
@endforelse
</tbody></table></div>
<div class="parent-pagination">{{ $assignments->links() }}</div>
@endsection
