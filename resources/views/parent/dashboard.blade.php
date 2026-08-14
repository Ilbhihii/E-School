@extends('layouts.parent')
@section('title', 'Tableau de bord')
@section('page_title', 'Tableau de bord Parent')

@section('content')
<div class="parent-hero">
    <div>
        <h2>Bonjour {{ auth()->user()->name }} 👋</h2>
        <p>Suivez l'emploi du temps, les présences, les devoirs et les résultats de vos enfants.</p>
    </div>
</div>

<div class="parent-stats">
    <div><strong>{{ $children->count() }}</strong><span>Enfant(s)</span></div>
    <div><strong>{{ $cards->sum('absences') }}</strong><span>Absences</span></div>
    <div><strong>{{ $cards->sum('pending_assignments') }}</strong><span>Devoirs sans note</span></div>
    <div><strong>{{ $cards->sum('results') }}</strong><span>Résultats</span></div>
</div>

<h3 class="parent-section-title">Mes enfants</h3>

<div class="parent-children">
@forelse($cards as $card)
    @php $student = $card->student; @endphp
    <article class="parent-child-card">
        <div class="parent-child-head">
            <span>{{ strtoupper(mb_substr($student->name, 0, 1)) }}</span>
            <div><h4>{{ $student->name }}</h4><small>{{ $student->parent_relationship ?? 'Enfant' }}</small></div>
        </div>

        <div class="parent-paths">
            @forelse($card->paths as $path)
                <span>{{ $path->subject_name ?? 'Matière' }} · {{ $path->level_name ?? 'Niveau' }} · {{ $path->class_name ?? 'Classe' }} @if($path->slot_code) · {{ $path->slot_code }} @endif</span>
            @empty
                <span>Aucun parcours assigné</span>
            @endforelse
        </div>

        <a href="{{ route('parent.children.show', $student) }}" class="parent-btn">Voir le suivi <i class="bi bi-arrow-right"></i></a>
    </article>
@empty
    <div class="parent-empty">Aucun enfant n'est encore associé à ce compte.</div>
@endforelse
</div>
@endsection
