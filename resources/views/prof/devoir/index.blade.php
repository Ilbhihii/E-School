@extends('layouts.prof')

@section('title', 'Gestion des devoirs')
@section('page_title', 'Devoirs')
@section('breadcrumb', 'Devoirs publiés')

@section('content')
@php
    $devoirCollection = method_exists($devoirs, 'getCollection') ? $devoirs->getCollection() : collect($devoirs);
    $today = now()->startOfDay();
    $upcomingCount = $devoirCollection->filter(fn ($item) => $item->due_date && \Carbon\Carbon::parse($item->due_date)->startOfDay()->gte($today))->count();
    $expiredCount = $devoirCollection->filter(fn ($item) => $item->due_date && \Carbon\Carbon::parse($item->due_date)->startOfDay()->lt($today))->count();
    $totalCount = method_exists($devoirs, 'total') ? $devoirs->total() : $devoirCollection->count();
@endphp

<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-file-earmark-check-fill"></i> Activités à rendre</span>
        <h1 class="pp-page-title">Mes devoirs</h1>
        <p class="pp-page-description">
            Créez les devoirs de vos classes, suivez les échéances et mettez à jour les documents publiés.
        </p>
    </div>

    <div class="pp-page-actions">
        <a href="{{ route('prof.devoir.create', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-success">
            <i class="bi bi-plus-lg"></i>
            Nouveau devoir
        </a>
    </div>
</section>

<div class="pp-summary-grid">
    <article class="pp-summary-card is-green">
        <span class="pp-summary-icon"><i class="bi bi-file-earmark-check-fill"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $totalCount }}</strong><span class="pp-summary-label">Total devoirs</span></span>
    </article>
    <article class="pp-summary-card is-cyan">
        <span class="pp-summary-icon"><i class="bi bi-clock-history"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $upcomingCount }}</strong><span class="pp-summary-label">À venir sur cette page</span></span>
    </article>
    <article class="pp-summary-card is-yellow">
        <span class="pp-summary-icon"><i class="bi bi-calendar-x-fill"></i></span>
        <span class="pp-summary-copy"><strong class="pp-summary-value">{{ $expiredCount }}</strong><span class="pp-summary-label">Échéances passées</span></span>
    </article>
</div>

<div class="pp-toolbar">
    <form method="GET" action="{{ route('prof.devoir.index') }}" class="pp-filter-select">
        <label for="courseFilter" class="pp-label"><i class="bi bi-funnel"></i> Filtrer par cours</label>
        <select id="courseFilter" name="course_id" class="adm-form-select" onchange="this.form.submit()">
            <option value="">Tous les cours</option>
            @foreach($courses as $courseOption)
                <option value="{{ $courseOption->id }}" {{ (string) ($course_id ?? '') === (string) $courseOption->id ? 'selected' : '' }}>
                    {{ $courseOption->title }}
                </option>
            @endforeach
        </select>
    </form>

    @if($course)
        <span class="pp-soft-chip"><i class="bi bi-book"></i> {{ $course->title }}</span>
    @endif
</div>

<section class="pp-panel">
    <header class="pp-panel-head">
        <div class="pp-panel-title-wrap">
            <h2 class="pp-panel-title"><i class="bi bi-grid-3x3-gap-fill"></i> Devoirs publiés</h2>
            <p class="pp-panel-subtitle">Modifiez, téléchargez ou supprimez un devoir.</p>
        </div>
        <span class="pp-panel-meta">{{ $totalCount }} devoir(s)</span>
    </header>

    <div class="pp-panel-body">
        <div class="pp-devoir-grid">
            @forelse($devoirs as $devoir)
                @php
                    $dueDate = $devoir->due_date ? \Carbon\Carbon::parse($devoir->due_date) : null;
                    $isPast = $dueDate ? $dueDate->copy()->startOfDay()->lt($today) : false;
                @endphp

                <article class="pp-devoir-card">
                    <div class="pp-devoir-top">
                        <span class="pp-devoir-icon"><i class="bi bi-file-earmark-text-fill"></i></span>
                        <span class="adm-badge {{ $isPast ? 'adm-badge-danger' : 'adm-badge-success' }}">
                            <i class="bi {{ $isPast ? 'bi-calendar-x' : 'bi-calendar-check' }}"></i>
                            {{ $isPast ? 'Échéance passée' : 'À rendre' }}
                        </span>
                    </div>

                    <h3 class="pp-devoir-title">{{ $devoir->title }}</h3>

                    <div class="pp-devoir-meta">
                        <span class="subject-chip"><i class="bi bi-people"></i> {{ $devoir->classRoom?->name ?? 'Classe non définie' }}</span>
                        <span class="subject-chip"><i class="bi bi-calendar3"></i> {{ $dueDate ? $dueDate->format('d/m/Y') : 'Sans date' }}</span>
                    </div>

                    <div class="pp-devoir-actions">
                        <div>
                            @if($devoir->file)
                                <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" rel="noopener" class="adm-btn adm-btn-ghost adm-btn-sm">
                                    <i class="bi bi-file-earmark-pdf"></i> Document
                                </a>
                            @else
                                <span class="adm-badge adm-badge-gray">Sans fichier</span>
                            @endif
                        </div>

                        <div class="pp-devoir-action-group">
                            <a href="{{ route('prof.devoir.edit', $devoir) }}" class="adm-btn adm-btn-warning adm-btn-sm" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('prof.devoir.destroy', $devoir) }}" onsubmit="return confirm('Confirmer la suppression de ce devoir ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="pp-empty" style="grid-column:1/-1;">
                    <div>
                        <span class="pp-empty-icon"><i class="bi bi-file-earmark-plus"></i></span>
                        <h3>Aucun devoir publié</h3>
                        <p>Créez votre premier devoir pour le rendre disponible à une classe.</p>
                        <a href="{{ route('prof.devoir.create', ['course_id' => $course_id ?? null]) }}" class="adm-btn adm-btn-success mt-3">
                            <i class="bi bi-plus-lg"></i> Créer un devoir
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @if(method_exists($devoirs, 'links') && $devoirs->hasPages())
        <div class="pp-pagination">{{ $devoirs->appends(request()->query())->links() }}</div>
    @endif
</section>
@endsection
