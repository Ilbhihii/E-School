@extends('layouts.admin')

@section('title', 'Disponibilités professeurs')
@section('page_title', 'Disponibilités professeurs')
@section('breadcrumb', 'Professeurs → Disponibilités → Construction du planning')

@section('content')
<div class="prof-av-page">
    <div class="prof-av-hero">
        <div>
            <span class="prof-av-kicker">
                <i class="bi bi-stars"></i>
                Outil de préparation du planning
            </span>

            <h1>Disponibilités des professeurs</h1>

            <p>
                Centralisez les horaires reçus des professeurs par créneaux
                de 1h30, puis utilisez le récapitulatif pour construire
                l'emploi du temps sans chercher dans les messages WhatsApp.
            </p>
        </div>

        <div class="prof-av-hero-actions">
            <a
                href="{{ route('admin.users.prof-assignments') }}"
                class="prof-av-btn prof-av-btn-soft"
            >
                <i class="bi bi-person-badge"></i>
                Affectations
            </a>

            <a
                href="{{ route('admin.schedule.index') }}"
                class="prof-av-btn prof-av-btn-primary"
            >
                <i class="bi bi-calendar3-week"></i>
                Construire le planning
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="prof-av-alert success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="prof-av-alert danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="prof-av-stats">
        <article class="prof-av-stat">
            <div class="prof-av-stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <span>Professeurs</span>
                <strong>{{ $stats['total_professors'] }}</strong>
                <small>Total enregistré</small>
            </div>
        </article>

        <article class="prof-av-stat is-success">
            <div class="prof-av-stat-icon"><i class="bi bi-calendar2-check-fill"></i></div>
            <div>
                <span>Disponibilités reçues</span>
                <strong>{{ $stats['completed'] }}</strong>
                <small>Professeurs renseignés</small>
            </div>
        </article>

        <article class="prof-av-stat is-warning">
            <div class="prof-av-stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <span>En attente</span>
                <strong>{{ $stats['pending'] }}</strong>
                <small>Retour à récupérer</small>
            </div>
        </article>

        <article class="prof-av-stat is-info">
            <div class="prof-av-stat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div>
                <span>Créneaux disponibles</span>
                <strong>{{ $stats['availability_slots'] }}</strong>
                <small>Blocs de 1h30</small>
            </div>
        </article>
    </div>

    <div class="prof-av-tabs" role="tablist">
        <button type="button" class="prof-av-tab active" data-av-tab="editor">
            <i class="bi bi-pencil-square"></i>
            Saisir les disponibilités
        </button>
        <button type="button" class="prof-av-tab" data-av-tab="week">
            <i class="bi bi-calendar-week"></i>
            Vue hebdomadaire
        </button>
        <button type="button" class="prof-av-tab" data-av-tab="summary">
            <i class="bi bi-table"></i>
            Tableau récapitulatif
        </button>
    </div>

    <section class="prof-av-panel active" data-av-panel="editor">
        @if($selectedProfessor)
            <div class="prof-av-editor-head">
                <div>
                    <span class="prof-av-section-kicker">Disponibilités déclarées</span>
                    <h2>Renseigner un professeur</h2>
                    <p>
                        Cochez uniquement les créneaux pendant lesquels
                        le professeur peut réellement assurer un cours.
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.professor-availability.index') }}" class="prof-av-prof-select-form">
                    <label for="professor_id">Professeur</label>
                    <select name="professor_id" id="professor_id" class="prof-av-select" onchange="this.form.submit()">
                        @foreach($professors as $professor)
                            <option
                                value="{{ $professor->id }}"
                                {{ (int) $selectedProfessor->id === (int) $professor->id ? 'selected' : '' }}
                            >
                                {{ $professor->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="prof-av-selected-prof">
                <div class="prof-av-avatar">
                    {{ mb_strtoupper(mb_substr($selectedProfessor->name ?: '?', 0, 1)) }}
                </div>
                <div class="prof-av-selected-main">
                    <strong>{{ $selectedProfessor->name }}</strong>
                    <span>{{ $selectedProfessor->email }}</span>
                </div>

                <div class="prof-av-teaching-inline">
                    <span>Peut intervenir sur</span>
                    <div>
                        @forelse(($teachingSummary[$selectedProfessor->id] ?? collect())->take(4) as $teaching)
                            <span class="prof-av-path-chip">{{ $teaching }}</span>
                        @empty
                            <span class="prof-av-empty-chip">Aucune affectation pédagogique définie</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('admin.professor-availability.update', $selectedProfessor) }}"
                id="profAvailabilityForm"
            >
                @csrf
                @method('PUT')

                <div class="prof-av-grid-toolbar">
                    <div>
                        <strong>Créneaux de 1h30</strong>
                        <span>09:00 → 19:30</span>
                    </div>
                    <button type="button" class="prof-av-text-btn" id="clearAvailability">
                        <i class="bi bi-eraser"></i>
                        Tout décocher
                    </button>
                </div>

                <div class="prof-av-scroll">
                    <div class="prof-av-editor-grid">
                        <div class="prof-av-grid-corner">
                            Horaire
                        </div>

                        @foreach($days as $dayNumber => $dayLabel)
                            <div class="prof-av-day-header">
                                <strong>{{ mb_substr($dayLabel, 0, 3) }}</strong>
                                <button
                                    type="button"
                                    class="prof-av-day-toggle"
                                    data-day="{{ $dayNumber }}"
                                    title="Sélectionner / désélectionner la journée"
                                >
                                    Tout
                                </button>
                            </div>
                        @endforeach

                        @foreach($timeSlots as $slot)
                            <div class="prof-av-time-label">
                                <span>C{{ $slot['index'] }}</span>
                                <strong>{{ $slot['start'] }}</strong>
                                <small>{{ $slot['end'] }}</small>
                            </div>

                            @foreach($days as $dayNumber => $dayLabel)
                                @php
                                    $key = $dayNumber . '|' . $slot['start'] . '|' . $slot['end'];
                                    $checked = $selectedAvailabilityKeys->contains($key);
                                @endphp

                                <label
                                    class="prof-av-slot-check {{ $checked ? 'is-checked' : '' }}"
                                    data-day-cell="{{ $dayNumber }}"
                                >
                                    <input
                                        type="checkbox"
                                        name="slots[]"
                                        value="{{ $key }}"
                                        data-day-checkbox="{{ $dayNumber }}"
                                        {{ $checked ? 'checked' : '' }}
                                    >
                                    <span class="prof-av-check-icon">
                                        <i class="bi bi-check-lg"></i>
                                    </span>
                                    <small>Disponible</small>
                                </label>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <div class="prof-av-form-footer">
                    <div class="prof-av-form-hint">
                        <i class="bi bi-info-circle"></i>
                        Si aucune disponibilité n'est encore reçue,
                        laissez le professeur vide et revenez plus tard.
                    </div>

                    <button type="submit" class="prof-av-btn prof-av-btn-primary">
                        <i class="bi bi-cloud-check-fill"></i>
                        Enregistrer les disponibilités
                    </button>
                </div>
            </form>

            @if(($availabilityByProfessor->get($selectedProfessor->id, collect()))->isNotEmpty())
                <form
                    method="POST"
                    action="{{ route('admin.professor-availability.destroy', $selectedProfessor) }}"
                    class="prof-av-clear-form"
                    onsubmit="return confirm('Effacer toutes les disponibilités de ce professeur ?')"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="prof-av-danger-link">
                        <i class="bi bi-trash3"></i>
                        Effacer toutes les disponibilités de {{ $selectedProfessor->name }}
                    </button>
                </form>
            @endif
        @else
            <div class="prof-av-empty-state">
                <i class="bi bi-person-plus"></i>
                <h3>Aucun professeur</h3>
                <p>Créez d'abord un compte professeur pour renseigner ses disponibilités.</p>
                <a href="{{ route('admin.professors.create') }}" class="prof-av-btn prof-av-btn-primary">
                    Ajouter un professeur
                </a>
            </div>
        @endif
    </section>

    <section class="prof-av-panel" data-av-panel="week">
        <div class="prof-av-section-head">
            <div>
                <span class="prof-av-section-kicker">Vue type agenda</span>
                <h2>Qui est disponible à quel moment ?</h2>
                <p>
                    Chaque case affiche les professeurs disponibles
                    pendant le créneau correspondant.
                </p>
            </div>
        </div>

        <div class="prof-av-scroll">
            <div class="prof-av-week-grid">
                <div class="prof-av-week-corner">Horaire</div>
                @foreach($days as $dayLabel)
                    <div class="prof-av-week-day">{{ $dayLabel }}</div>
                @endforeach

                @foreach($timeSlots as $slot)
                    <div class="prof-av-week-time">
                        <strong>{{ $slot['start'] }}</strong>
                        <span>{{ $slot['end'] }}</span>
                    </div>

                    @foreach($days as $dayNumber => $dayLabel)
                        @php
                            $cell = $availabilityMatrix->get(
                                $dayNumber . '|' . $slot['start'],
                                collect()
                            );
                        @endphp

                        <div class="prof-av-week-cell {{ $cell->isNotEmpty() ? 'has-professors' : '' }}">
                            @if($cell->isEmpty())
                                <span class="prof-av-free-empty">—</span>
                            @else
                                @foreach($cell->take(3) as $availability)
                                    <a
                                        href="{{ route('admin.professor-availability.index', ['professor_id' => $availability->prof_id]) }}"
                                        class="prof-av-prof-chip"
                                    >
                                        {{ optional($availability->professor)->name ?: 'Professeur' }}
                                    </a>
                                @endforeach

                                @if($cell->count() > 3)
                                    <span class="prof-av-more-chip">
                                        +{{ $cell->count() - 3 }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>

    <section class="prof-av-panel" data-av-panel="summary">
        <div class="prof-av-section-head prof-av-summary-head">
            <div>
                <span class="prof-av-section-kicker">Récapitulatif opérationnel</span>
                <h2>Qui peut faire quoi et quand ?</h2>
                <p>
                    Les affectations pédagogiques sont affichées avec les disponibilités,
                    pour faciliter la construction du planning final.
                </p>
            </div>

            <div class="prof-av-search-wrap">
                <i class="bi bi-search"></i>
                <input
                    type="search"
                    id="profAvailabilitySearch"
                    placeholder="Rechercher un professeur..."
                >
            </div>
        </div>

        <div class="prof-av-table-wrap">
            <table class="prof-av-table">
                <thead>
                    <tr>
                        <th>Professeur</th>
                        <th>Peut intervenir sur</th>
                        <th>Disponibilités</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="profAvailabilitySummaryBody">
                    @foreach($professors as $professor)
                        @php
                            $profAvailabilities = $availabilityByProfessor->get($professor->id, collect());
                            $profTeaching = $teachingSummary[$professor->id] ?? collect();
                        @endphp

                        <tr data-prof-row data-search="{{ mb_strtolower($professor->name . ' ' . $professor->email) }}">
                            <td>
                                <div class="prof-av-person-cell">
                                    <span class="prof-av-mini-avatar">
                                        {{ mb_strtoupper(mb_substr($professor->name ?: '?', 0, 1)) }}
                                    </span>
                                    <div>
                                        <strong>{{ $professor->name }}</strong>
                                        <span>{{ $professor->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="prof-av-path-list">
                                    @forelse($profTeaching->take(3) as $teaching)
                                        <span>{{ $teaching }}</span>
                                    @empty
                                        <em>À définir</em>
                                    @endforelse
                                    @if($profTeaching->count() > 3)
                                        <small>+{{ $profTeaching->count() - 3 }} autre(s)</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @if($profAvailabilities->isEmpty())
                                    <span class="prof-av-pending-text">
                                        <i class="bi bi-clock-history"></i>
                                        En attente du retour
                                    </span>
                                @else
                                    <div class="prof-av-day-summary">
                                        @foreach($days as $dayNumber => $dayLabel)
                                            @php
                                                $dayItems = $profAvailabilities
                                                    ->where('day_of_week', $dayNumber)
                                                    ->sortBy('start_time');
                                            @endphp
                                            @if($dayItems->isNotEmpty())
                                                <span
                                                    title="{{ $dayItems->map(function($item){ return $item->range_label; })->implode(', ') }}"
                                                >
                                                    {{ mb_substr($dayLabel, 0, 3) }}
                                                    <strong>{{ $dayItems->count() }}</strong>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($profAvailabilities->isNotEmpty())
                                    <span class="prof-av-status is-complete">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Reçu
                                    </span>
                                @else
                                    <span class="prof-av-status is-pending">
                                        <i class="bi bi-hourglass-split"></i>
                                        En attente
                                    </span>
                                @endif
                            </td>

                            <td class="prof-av-action-cell">
                                <a
                                    href="{{ route('admin.professor-availability.index', ['professor_id' => $professor->id]) }}"
                                    class="prof-av-icon-btn"
                                    title="Modifier les disponibilités"
                                >
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="prof-av-no-result" id="profAvailabilityNoResult" hidden>
            Aucun professeur ne correspond à cette recherche.
        </div>
    </section>
</div>

<style>
.prof-av-page{display:flex;flex-direction:column;gap:1.15rem}.prof-av-hero{position:relative;display:flex;justify-content:space-between;gap:2rem;align-items:center;padding:1.6rem;border:1px solid rgba(124,58,237,.16);border-radius:22px;background:radial-gradient(circle at 88% 18%,rgba(124,58,237,.18),transparent 34%),linear-gradient(145deg,rgba(15,23,42,.96),rgba(9,15,28,.95));overflow:hidden}.prof-av-hero:after{content:"";position:absolute;width:190px;height:190px;right:-70px;bottom:-110px;border-radius:50%;background:rgba(59,130,246,.08);filter:blur(4px)}.prof-av-kicker,.prof-av-section-kicker{display:inline-flex;align-items:center;gap:.45rem;color:#a78bfa;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em}.prof-av-hero h1{margin:.45rem 0 .45rem;font-size:clamp(1.45rem,2vw,2rem);color:#fff}.prof-av-hero p,.prof-av-section-head p,.prof-av-editor-head p{max-width:760px;margin:0;color:var(--adm-text-muted);font-size:.82rem;line-height:1.6}.prof-av-hero-actions{position:relative;z-index:2;display:flex;gap:.6rem;flex-wrap:wrap}.prof-av-btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;min-height:42px;padding:.65rem .95rem;border:0;border-radius:12px;text-decoration:none;font-size:.75rem;font-weight:800;cursor:pointer;transition:.2s ease}.prof-av-btn:hover{transform:translateY(-1px)}.prof-av-btn-primary{color:#fff;background:linear-gradient(135deg,#7c3aed,#4f46e5);box-shadow:0 10px 24px rgba(79,70,229,.22)}.prof-av-btn-soft{color:#dbeafe;background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.075)}.prof-av-alert{display:flex;align-items:center;gap:.6rem;padding:.8rem 1rem;border-radius:13px;font-size:.76rem}.prof-av-alert.success{color:#bbf7d0;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2)}.prof-av-alert.danger{color:#fecaca;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2)}.prof-av-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem}.prof-av-stat{display:flex;gap:.8rem;align-items:center;padding:1rem;border-radius:17px;border:1px solid rgba(255,255,255,.055);background:rgba(15,23,42,.74)}.prof-av-stat-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:13px;color:#c4b5fd;background:rgba(124,58,237,.12);font-size:1rem}.prof-av-stat.is-success .prof-av-stat-icon{color:#86efac;background:rgba(34,197,94,.1)}.prof-av-stat.is-warning .prof-av-stat-icon{color:#fde68a;background:rgba(245,158,11,.1)}.prof-av-stat.is-info .prof-av-stat-icon{color:#93c5fd;background:rgba(59,130,246,.1)}.prof-av-stat div:last-child{min-width:0;display:grid}.prof-av-stat span{color:var(--adm-text-muted);font-size:.64rem}.prof-av-stat strong{color:#fff;font-size:1.3rem;line-height:1.15}.prof-av-stat small{color:rgba(255,255,255,.34);font-size:.58rem}.prof-av-tabs{display:flex;gap:.45rem;padding:.35rem;border:1px solid rgba(255,255,255,.05);border-radius:14px;background:rgba(15,23,42,.58);width:max-content;max-width:100%;overflow:auto}.prof-av-tab{display:flex;align-items:center;gap:.45rem;padding:.6rem .8rem;border:0;border-radius:10px;color:var(--adm-text-muted);background:transparent;font-size:.7rem;font-weight:800;white-space:nowrap;cursor:pointer}.prof-av-tab.active{color:#fff;background:rgba(124,58,237,.18);box-shadow:inset 0 0 0 1px rgba(167,139,250,.12)}.prof-av-panel{display:none;padding:1.15rem;border:1px solid rgba(255,255,255,.055);border-radius:19px;background:rgba(10,17,31,.77)}.prof-av-panel.active{display:block}.prof-av-editor-head,.prof-av-section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1rem}.prof-av-editor-head h2,.prof-av-section-head h2{margin:.2rem 0 .2rem;color:#fff;font-size:1.05rem}.prof-av-prof-select-form{min-width:250px}.prof-av-prof-select-form label{display:block;margin-bottom:.3rem;color:var(--adm-text-muted);font-size:.61rem;font-weight:700}.prof-av-select{width:100%;min-height:42px;padding:.55rem .75rem;border:1px solid rgba(255,255,255,.08);border-radius:11px;color:#fff;background:#101a2d;outline:none;font-size:.75rem}.prof-av-selected-prof{display:grid;grid-template-columns:auto minmax(150px,.7fr) minmax(280px,1.8fr);gap:.8rem;align-items:center;margin-bottom:1rem;padding:.8rem;border-radius:14px;background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.05)}.prof-av-avatar,.prof-av-mini-avatar{display:grid;place-items:center;border-radius:13px;color:#fff;background:linear-gradient(135deg,#7c3aed,#2563eb);font-weight:900}.prof-av-avatar{width:48px;height:48px;font-size:1rem}.prof-av-selected-main{display:grid}.prof-av-selected-main strong{color:#fff;font-size:.84rem}.prof-av-selected-main span{color:var(--adm-text-muted);font-size:.65rem}.prof-av-teaching-inline>span{display:block;margin-bottom:.35rem;color:var(--adm-text-muted);font-size:.58rem;text-transform:uppercase;letter-spacing:.05em}.prof-av-teaching-inline>div{display:flex;gap:.35rem;flex-wrap:wrap}.prof-av-path-chip,.prof-av-empty-chip{display:inline-flex;padding:.3rem .48rem;border-radius:8px;font-size:.58rem}.prof-av-path-chip{color:#bfdbfe;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.13)}.prof-av-empty-chip{color:#94a3b8;background:rgba(148,163,184,.07)}.prof-av-grid-toolbar{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin:.4rem 0 .65rem}.prof-av-grid-toolbar>div{display:grid}.prof-av-grid-toolbar strong{color:#fff;font-size:.75rem}.prof-av-grid-toolbar span{color:var(--adm-text-muted);font-size:.6rem}.prof-av-text-btn{border:0;background:transparent;color:#a78bfa;font-size:.66rem;font-weight:800;cursor:pointer}.prof-av-scroll{width:100%;overflow:auto;border-radius:14px}.prof-av-editor-grid,.prof-av-week-grid{display:grid;grid-template-columns:100px repeat(7,minmax(112px,1fr));min-width:900px;border:1px solid rgba(255,255,255,.05);border-radius:14px;overflow:hidden}.prof-av-grid-corner,.prof-av-day-header,.prof-av-time-label,.prof-av-slot-check,.prof-av-week-corner,.prof-av-week-day,.prof-av-week-time,.prof-av-week-cell{border-right:1px solid rgba(255,255,255,.045);border-bottom:1px solid rgba(255,255,255,.045)}.prof-av-grid-corner,.prof-av-week-corner{display:grid;place-items:center;color:#64748b;background:#0d1627;font-size:.58rem;text-transform:uppercase;letter-spacing:.07em}.prof-av-day-header{display:flex;align-items:center;justify-content:space-between;gap:.3rem;padding:.6rem;background:#0d1627}.prof-av-day-header strong{color:#e2e8f0;font-size:.68rem;text-transform:capitalize}.prof-av-day-toggle{padding:.2rem .35rem;border:0;border-radius:6px;color:#a78bfa;background:rgba(124,58,237,.09);font-size:.52rem;cursor:pointer}.prof-av-time-label{min-height:66px;display:grid;align-content:center;padding:.45rem .55rem;background:rgba(13,22,39,.78)}.prof-av-time-label span{color:#a78bfa;font-size:.5rem;font-weight:900}.prof-av-time-label strong{color:#fff;font-size:.7rem}.prof-av-time-label small{color:#64748b;font-size:.58rem}.prof-av-slot-check{position:relative;min-height:66px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.2rem;color:#64748b;background:rgba(255,255,255,.012);cursor:pointer;transition:.18s ease}.prof-av-slot-check:hover{background:rgba(124,58,237,.05)}.prof-av-slot-check input{position:absolute;opacity:0;pointer-events:none}.prof-av-check-icon{width:23px;height:23px;display:grid;place-items:center;border:1px solid rgba(148,163,184,.18);border-radius:7px;background:rgba(148,163,184,.04);font-size:.7rem}.prof-av-slot-check small{font-size:.52rem}.prof-av-slot-check.is-checked{color:#bbf7d0;background:linear-gradient(145deg,rgba(34,197,94,.11),rgba(16,185,129,.045))}.prof-av-slot-check.is-checked .prof-av-check-icon{color:#052e16;background:#4ade80;border-color:#4ade80}.prof-av-form-footer{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-top:.85rem}.prof-av-form-hint{display:flex;gap:.4rem;align-items:flex-start;max-width:620px;color:var(--adm-text-muted);font-size:.61rem;line-height:1.45}.prof-av-clear-form{margin-top:.65rem;text-align:right}.prof-av-danger-link{border:0;background:transparent;color:#fca5a5;font-size:.61rem;cursor:pointer}.prof-av-week-grid{grid-template-columns:86px repeat(7,minmax(145px,1fr))}.prof-av-week-day{padding:.65rem;text-align:center;color:#e2e8f0;background:#0d1627;font-size:.67rem;font-weight:800}.prof-av-week-time{min-height:82px;display:grid;align-content:center;padding:.55rem;background:#0d1627}.prof-av-week-time strong{color:#fff;font-size:.68rem}.prof-av-week-time span{color:#64748b;font-size:.56rem}.prof-av-week-cell{min-height:82px;display:flex;align-content:flex-start;flex-wrap:wrap;gap:.3rem;padding:.45rem;background:rgba(255,255,255,.01)}.prof-av-week-cell.has-professors{background:rgba(59,130,246,.025)}.prof-av-free-empty{margin:auto;color:rgba(148,163,184,.18)}.prof-av-prof-chip,.prof-av-more-chip{height:max-content;padding:.33rem .42rem;border-radius:8px;text-decoration:none;font-size:.57rem;font-weight:750}.prof-av-prof-chip{color:#dbeafe;background:rgba(59,130,246,.14);border:1px solid rgba(96,165,250,.13)}.prof-av-prof-chip:hover{color:#fff;background:rgba(59,130,246,.22)}.prof-av-more-chip{color:#c4b5fd;background:rgba(124,58,237,.1)}.prof-av-summary-head{align-items:center}.prof-av-search-wrap{position:relative;min-width:260px}.prof-av-search-wrap i{position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#64748b;font-size:.75rem}.prof-av-search-wrap input{width:100%;min-height:40px;padding:.55rem .7rem .55rem 2rem;border:1px solid rgba(255,255,255,.07);border-radius:10px;color:#fff;background:#0d1627;outline:none;font-size:.7rem}.prof-av-table-wrap{overflow:auto;border:1px solid rgba(255,255,255,.045);border-radius:14px}.prof-av-table{width:100%;min-width:900px;border-collapse:collapse}.prof-av-table th{padding:.7rem;text-align:left;color:#64748b;background:#0d1627;font-size:.57rem;text-transform:uppercase;letter-spacing:.055em}.prof-av-table td{padding:.75rem;border-top:1px solid rgba(255,255,255,.04);vertical-align:middle}.prof-av-person-cell{display:flex;align-items:center;gap:.55rem}.prof-av-mini-avatar{width:34px;height:34px;font-size:.7rem;border-radius:10px}.prof-av-person-cell>div{display:grid}.prof-av-person-cell strong{color:#fff;font-size:.7rem}.prof-av-person-cell span{color:#64748b;font-size:.58rem}.prof-av-path-list{display:flex;gap:.3rem;flex-wrap:wrap;max-width:430px}.prof-av-path-list span{padding:.27rem .38rem;border-radius:7px;color:#bfdbfe;background:rgba(59,130,246,.07);font-size:.54rem}.prof-av-path-list em{color:#64748b;font-size:.58rem;font-style:normal}.prof-av-path-list small{align-self:center;color:#a78bfa;font-size:.52rem}.prof-av-day-summary{display:flex;gap:.28rem;flex-wrap:wrap}.prof-av-day-summary>span{display:inline-flex;align-items:center;gap:.24rem;padding:.27rem .36rem;border-radius:7px;color:#cbd5e1;background:rgba(255,255,255,.035);font-size:.54rem}.prof-av-day-summary strong{color:#86efac}.prof-av-pending-text{display:inline-flex;align-items:center;gap:.3rem;color:#fbbf24;font-size:.58rem}.prof-av-status{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .43rem;border-radius:999px;font-size:.55rem;font-weight:800}.prof-av-status.is-complete{color:#86efac;background:rgba(34,197,94,.08)}.prof-av-status.is-pending{color:#fde68a;background:rgba(245,158,11,.08)}.prof-av-action-cell{text-align:right}.prof-av-icon-btn{width:32px;height:32px;display:inline-grid;place-items:center;border-radius:9px;color:#c4b5fd;background:rgba(124,58,237,.08);text-decoration:none}.prof-av-no-result{text-align:center;padding:1rem;color:#64748b;font-size:.68rem}.prof-av-empty-state{text-align:center;padding:3rem 1rem}.prof-av-empty-state>i{font-size:2rem;color:#7c3aed}.prof-av-empty-state h3{margin:.6rem 0 .2rem;color:#fff}.prof-av-empty-state p{color:var(--adm-text-muted);font-size:.7rem}.prof-av-empty-state .prof-av-btn{margin-top:.7rem}@media(max-width:1100px){.prof-av-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.prof-av-hero{align-items:flex-start;flex-direction:column}.prof-av-selected-prof{grid-template-columns:auto 1fr}.prof-av-teaching-inline{grid-column:1/-1}}@media(max-width:700px){.prof-av-stats{grid-template-columns:1fr}.prof-av-panel{padding:.8rem}.prof-av-editor-head,.prof-av-section-head,.prof-av-form-footer{align-items:stretch;flex-direction:column}.prof-av-prof-select-form,.prof-av-search-wrap{min-width:0;width:100%}.prof-av-hero-actions{width:100%}.prof-av-hero-actions .prof-av-btn{flex:1}.prof-av-selected-prof{grid-template-columns:auto 1fr}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = Array.from(document.querySelectorAll('[data-av-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-av-panel]'));

    function activateTab(name) {
        tabs.forEach(function (tab) {
            tab.classList.toggle('active', tab.dataset.avTab === name);
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('active', panel.dataset.avPanel === name);
        });
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activateTab(tab.dataset.avTab);
        });
    });

    const checkboxes = Array.from(
        document.querySelectorAll('[data-day-checkbox]')
    );

    function refreshCheckboxCard(input) {
        const label = input.closest('.prof-av-slot-check');
        if (label) {
            label.classList.toggle('is-checked', input.checked);
        }
    }

    checkboxes.forEach(function (input) {
        input.addEventListener('change', function () {
            refreshCheckboxCard(input);
        });
    });

    document.querySelectorAll('.prof-av-day-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const day = button.dataset.day;
            const dayInputs = checkboxes.filter(function (input) {
                return input.dataset.dayCheckbox === day;
            });
            const shouldCheck = dayInputs.some(function (input) {
                return !input.checked;
            });

            dayInputs.forEach(function (input) {
                input.checked = shouldCheck;
                refreshCheckboxCard(input);
            });
        });
    });

    const clearButton = document.getElementById('clearAvailability');
    if (clearButton) {
        clearButton.addEventListener('click', function () {
            checkboxes.forEach(function (input) {
                input.checked = false;
                refreshCheckboxCard(input);
            });
        });
    }

    const search = document.getElementById('profAvailabilitySearch');
    const rows = Array.from(document.querySelectorAll('[data-prof-row]'));
    const noResult = document.getElementById('profAvailabilityNoResult');

    if (search) {
        search.addEventListener('input', function () {
            const query = search.value.trim().toLocaleLowerCase('fr');
            let visible = 0;

            rows.forEach(function (row) {
                const haystack = (row.dataset.search || '').toLocaleLowerCase('fr');
                const show = !query || haystack.includes(query);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (noResult) {
                noResult.hidden = visible !== 0;
            }
        });
    }
});
</script>
@endsection
