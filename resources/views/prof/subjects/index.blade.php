@extends('layouts.prof')

@section('title', 'Navigation par Matières')
@section('page_title', 'Matières')
@section('breadcrumb', 'Navigation pédagogique')

@section('content')
<style>
.subjects-hero{position:relative;overflow:hidden;padding:1.6rem;border:1px solid rgba(139,92,246,.2);border-radius:20px;background:linear-gradient(135deg,rgba(124,58,237,.18),rgba(37,99,235,.08));margin-bottom:1.5rem}
.subjects-hero:after{content:"";position:absolute;width:240px;height:240px;border-radius:50%;right:-80px;top:-120px;background:rgba(167,139,250,.12);filter:blur(4px)}
.subjects-hero-content{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.subjects-title{margin:0;color:#f8fafc;font-size:clamp(1.35rem,3vw,2rem);font-weight:800;letter-spacing:-.03em}
.subjects-subtitle{margin:.45rem 0 0;color:#94a3b8;font-size:.88rem}
.subjects-count{display:flex;align-items:center;gap:.65rem;padding:.65rem .9rem;border-radius:12px;background:rgba(15,23,42,.4);border:1px solid rgba(255,255,255,.08);color:#c4b5fd;font-weight:700}
.subjects-tools{display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem}
.subjects-search{position:relative;max-width:420px;width:100%}
.subjects-search i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#64748b}
.subjects-search input{width:100%;padding:.72rem 1rem .72rem 2.55rem;border-radius:12px;border:1px solid rgba(255,255,255,.08);background:rgba(15,23,42,.55);color:#e2e8f0;outline:none;transition:.2s}
.subjects-search input:focus{border-color:rgba(139,92,246,.55);box-shadow:0 0 0 3px rgba(139,92,246,.1)}
.subject-card{height:100%;overflow:hidden;border-radius:18px;border:1px solid rgba(255,255,255,.08);background:rgba(15,23,42,.58);box-shadow:0 10px 30px rgba(2,6,23,.16);transition:transform .25s,border-color .25s,box-shadow .25s}
.subject-card:hover{transform:translateY(-6px);border-color:var(--subject-color);box-shadow:0 18px 38px rgba(2,6,23,.28)}
.subject-cover{height:112px;position:relative;display:flex;align-items:center;justify-content:center;background:var(--subject-gradient);overflow:hidden}
.subject-cover:before,.subject-cover:after{content:"";position:absolute;border-radius:50%;background:rgba(255,255,255,.09)}
.subject-cover:before{width:140px;height:140px;right:-45px;top:-70px}.subject-cover:after{width:80px;height:80px;left:-25px;bottom:-40px}
.subject-icon{width:58px;height:58px;display:grid;place-items:center;border-radius:16px;background:rgba(255,255,255,.16);backdrop-filter:blur(8px);color:#fff;font-size:1.7rem;box-shadow:0 8px 24px rgba(0,0,0,.12);position:relative;z-index:1}
.subject-body{padding:1.15rem}.subject-name{color:#f1f5f9;font-size:1rem;font-weight:750;margin:0 0 .8rem}
.subject-meta{display:flex;gap:.55rem;flex-wrap:wrap;margin-bottom:1rem}.subject-chip{padding:.28rem .55rem;border-radius:8px;background:rgba(255,255,255,.05);color:#94a3b8;font-size:.7rem}
.subject-action{display:flex;align-items:center;justify-content:space-between;padding:.65rem .8rem;border-radius:10px;background:color-mix(in srgb,var(--subject-color) 15%,transparent);color:var(--subject-color);font-size:.78rem;font-weight:700;transition:.2s}.subject-card:hover .subject-action{color:#fff;background:var(--subject-color)}
.subjects-empty-filter{display:none;text-align:center;padding:3rem;color:#64748b}
html.light-mode .subjects-title{color:#172033}html.light-mode .subjects-subtitle{color:#64748b}html.light-mode .subject-card{background:#fff;border-color:#e2e8f0}html.light-mode .subject-name{color:#172033}html.light-mode .subjects-search input{background:#fff;color:#172033;border-color:#dbe3ef}
@media(max-width:575px){.subjects-hero{padding:1.2rem}.subjects-tools{display:block}.subject-cover{height:96px}}
</style>

<section class="subjects-hero">
    <div class="subjects-hero-content">
        <div>
            <h1 class="subjects-title"><i class="bi bi-journals me-2"></i>Navigation par matières</h1>
            <p class="subjects-subtitle">Choisissez une matière, puis parcourez ses niveaux et ses classes.</p>
        </div>
        <div class="subjects-count"><i class="bi bi-book"></i><span>{{ $subjects->count() }} matière(s)</span></div>
    </div>
</section>

@if($subjects->isNotEmpty())
<div class="subjects-tools">
    <div class="subjects-search">
        <i class="bi bi-search"></i>
        <input type="search" id="subjectSearch" placeholder="Rechercher une matière..." autocomplete="off">
    </div>
</div>
@endif

<div class="prof-card-grid" id="subjectsGrid">
    @forelse($subjects as $subject)
        @php
            $icons = ['book','calculator','flask','translate','globe2','palette','music-note-beamed','cpu','graph-up','pencil-square','journal-text','stars'];
            $themes = [
                ['#8B5CF6','linear-gradient(135deg,#6D28D9,#A78BFA)'],
                ['#10B981','linear-gradient(135deg,#047857,#34D399)'],
                ['#F59E0B','linear-gradient(135deg,#B45309,#FBBF24)'],
                ['#3B82F6','linear-gradient(135deg,#1D4ED8,#60A5FA)'],
                ['#EF4444','linear-gradient(135deg,#B91C1C,#F87171)'],
                ['#06B6D4','linear-gradient(135deg,#0E7490,#22D3EE)'],
            ];
            [$color, $gradient] = $themes[$loop->index % count($themes)];
            $levelCount = $subject->assigned_levels_count ?? 0;
        @endphp
        <div class="subject-item" data-name="{{ Str::lower($subject->name) }}">
            <a href="{{ route('prof.subjects.levels', $subject) }}" class="text-decoration-none">
                <article class="subject-card" style="--subject-color:{{ $color }};--subject-gradient:{{ $gradient }};">
                    <div class="subject-cover"><div class="subject-icon"><i class="bi bi-{{ $icons[$loop->index % count($icons)] }}"></i></div></div>
                    <div class="subject-body">
                        <h2 class="subject-name">{{ $subject->name }}</h2>
                        <div class="subject-meta">
                            <span class="subject-chip"><i class="bi bi-layers me-1"></i>{{ $levelCount }} niveau(x)</span>
                            <span class="subject-chip"><i class="bi bi-building me-1"></i>{{ $subject->assigned_classes_count ?? 0 }} classe(s)</span>
                        </div>
                        <div class="subject-action"><span>Explorer les niveaux</span><i class="bi bi-arrow-right"></i></div>
                    </div>
                </article>
            </a>
        </div>
    @empty
        <div class="prof-grid-empty"><div class="adm-card"><div class="adm-empty"><div class="adm-empty-icon"><i class="bi bi-inbox"></i></div><h3>Aucune matière</h3><p>Aucune matière n'est disponible.</p></div></div></div>
    @endforelse
</div>
<div class="subjects-empty-filter" id="subjectsEmpty"><i class="bi bi-search fs-2 d-block mb-2"></i>Aucune matière ne correspond à votre recherche.</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('subjectSearch');
    if (!search) return;
    search.addEventListener('input', function () {
        const query = this.value.trim().toLocaleLowerCase('fr');
        let visible = 0;
        document.querySelectorAll('.subject-item').forEach(function (item) {
            const show = item.dataset.name.includes(query);
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        document.getElementById('subjectsEmpty').style.display = visible ? 'none' : 'block';
    });
});
</script>
@endsection
