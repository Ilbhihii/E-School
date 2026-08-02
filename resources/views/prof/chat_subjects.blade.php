@extends('layouts.prof')

@section('title', 'Discussions')
@section('page_title', 'Discussions')
@section('breadcrumb', 'Échanges par matière')

@section('content')
<section class="pp-page-head">
    <div class="pp-page-copy">
        <span class="pp-eyebrow"><i class="bi bi-chat-square-dots-fill"></i> Communication</span>
        <h1 class="pp-page-title">Questions et discussions</h1>
        <p class="pp-page-description">
            Ouvrez une matière pour répondre aux étudiants ou utilisez la conversation privée avec l’administration.
        </p>
    </div>

    <div class="pp-page-actions">
        <span class="pp-soft-chip"><i class="bi bi-chat-left-text"></i> {{ isset($subjects) ? $subjects->count() : 0 }} discussion(s)</span>
    </div>
</section>

@if(isset($subjects) && $subjects->isNotEmpty())
    <div class="pp-toolbar">
        <div class="pp-search">
            <i class="bi bi-search"></i>
            <input type="search" id="chatSubjectSearch" class="adm-form-control" placeholder="Rechercher une matière..." autocomplete="off">
        </div>
    </div>
@endif

<div class="pp-chat-grid" id="chatSubjectGrid">
    @forelse($subjects ?? collect() as $subject)
        @php
            $isAdministration = mb_strtolower($subject->name) === 'administration';
            $themes = [
                'linear-gradient(135deg,#6d28d9,#8b5cf6)',
                'linear-gradient(135deg,#0369a1,#38bdf8)',
                'linear-gradient(135deg,#047857,#34d399)',
                'linear-gradient(135deg,#b45309,#fbbf24)',
                'linear-gradient(135deg,#be123c,#fb7185)',
            ];
            $gradient = $isAdministration
                ? 'linear-gradient(135deg,#1d4ed8,#7c3aed)'
                : $themes[$loop->index % count($themes)];
        @endphp

        <div class="pp-chat-item" data-name="{{ Str::lower($subject->name) }}">
            <a
                href="{{ route('prof.chat', $subject->id) }}"
                class="pp-chat-card {{ $isAdministration ? 'is-admin' : '' }}"
                style="--chat-gradient:{{ $gradient }};"
            >
                <div class="pp-chat-cover">
                    <i class="bi {{ $isAdministration ? 'bi-shield-lock-fill' : 'bi-journal-text' }}"></i>
                </div>
                <div class="pp-chat-body">
                    <h2>{{ $subject->name }}</h2>
                    <p>
                        {{ $isAdministration
                            ? 'Conversation privée avec l’équipe administrative.'
                            : 'Consultez les questions des étudiants pour cette matière.'
                        }}
                    </p>
                    <span class="pp-chat-open">
                        <span>Ouvrir la discussion</span>
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
            </a>
        </div>
    @empty
        <div class="pp-panel" style="grid-column:1/-1;">
            <div class="pp-empty">
                <div>
                    <span class="pp-empty-icon"><i class="bi bi-chat-dots"></i></span>
                    <h3>Aucune discussion disponible</h3>
                    <p>Les matières de discussion apparaîtront après votre affectation.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="pp-no-results" id="chatSubjectEmpty">
    <i class="bi bi-search fs-2 d-block mb-2"></i>
    Aucune discussion ne correspond à votre recherche.
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('chatSubjectSearch');
    const empty = document.getElementById('chatSubjectEmpty');
    if (!search || !empty) return;

    search.addEventListener('input', function () {
        const query = this.value.trim().toLocaleLowerCase('fr');
        let visible = 0;

        document.querySelectorAll('.pp-chat-item').forEach(function (item) {
            const show = (item.dataset.name || '').includes(query);
            item.style.display = show ? '' : 'none';
            if (show) visible += 1;
        });

        empty.style.display = visible ? 'none' : 'block';
    });
});
</script>
@endpush
@endsection
