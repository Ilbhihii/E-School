@extends('layouts.parent')
@section('title', 'Résultats')
@section('page_title', 'Résultats — ' . $student->name)
@section('content')
<div class="parent-hero"><div><h2>Résultats</h2><p>QCM, tests vocaux et tests écrits.</p></div></div>
@include('parent.children._tabs', ['student' => $student])

<h3 class="parent-section-title">QCM</h3>
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Test</th><th>Matière</th><th>Score</th><th>%</th></tr></thead><tbody>
@forelse($qcmResults as $result)
<tr><td>{{ optional($result->test)->title ?? 'Test' }}</td><td>{{ optional(optional($result->test)->subject)->name ?? '—' }}</td><td>{{ $result->score }} / {{ $result->total_questions ?: '—' }}</td><td>{{ $result->percentage !== null ? number_format((float)$result->percentage,1).' %' : '—' }}</td></tr>
@empty
<tr><td colspan="4">Aucun résultat QCM.</td></tr>
@endforelse
</tbody></table></div>

<h3 class="parent-section-title">Tests vocaux</h3>
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Matière</th><th>Niveau</th><th>Classe</th><th>Note</th><th>Commentaire</th></tr></thead><tbody>
@forelse($vocalResults as $submission)
<tr><td>{{ optional($submission->subject)->name ?? '—' }}</td><td>{{ optional($submission->level)->name ?? '—' }}</td><td>{{ optional($submission->classRoom)->name ?? '—' }}</td><td>{{ $submission->final_score !== null ? $submission->final_score : $submission->score }}</td><td>{{ $submission->teacher_comment ?: '—' }}</td></tr>
@empty
<tr><td colspan="5">Aucun résultat vocal.</td></tr>
@endforelse
</tbody></table></div>

<h3 class="parent-section-title">Tests écrits</h3>
<div class="parent-table-wrap"><table class="parent-table"><thead><tr><th>Test</th><th>Matière</th><th>Niveau</th><th>Classe</th><th>Score</th><th>État</th></tr></thead><tbody>
@forelse($writtenResults as $submission)
<tr><td>{{ $submission->test_title }}</td><td>{{ optional($submission->subject)->name ?? '—' }}</td><td>{{ optional($submission->level)->name ?? '—' }}</td><td>{{ optional($submission->classRoom)->name ?? '—' }}</td><td>{{ $submission->score }}</td><td>{{ $submission->statusLabel() }}</td></tr>
@empty
<tr><td colspan="6">Aucun résultat écrit.</td></tr>
@endforelse
</tbody></table></div>
@endsection
