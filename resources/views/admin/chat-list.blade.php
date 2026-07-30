@extends('layouts.admin')

@section('title', 'Chat Administration')
@section('page_title', 'Chat')
@section('breadcrumb', 'Centre de communication')

@section('content')

@php
    $totalMessages = $subjects->sum(
        fn ($subject) =>
            (int) $subject->messages_count
    );

    $activeSubjects = $subjects->filter(
        fn ($subject) =>
            (int) $subject->messages_count > 0
    )->count();
@endphp

<div class="adm-page-header chat-page-header">
    <div>
        <h1>
            <span class="chat-page-icon">
                <i class="bi bi-chat-dots-fill"></i>
            </span>

            Centre de communication
        </h1>

        <div class="subtitle">
            Gérez les discussions pédagogiques et les
            conversations privées avec l’administration.
        </div>
    </div>

    <div class="chat-header-statistics">
        <div>
            <strong>{{ $subjects->count() }}</strong>
            <span>Espaces</span>
        </div>

        <div>
            <strong>{{ $totalMessages }}</strong>
            <span>Messages</span>
        </div>

        <div>
            <strong>{{ $activeSubjects }}</strong>
            <span>Actifs</span>
        </div>
    </div>
</div>

@if(isset($subjects) && $subjects->isNotEmpty())
    <div class="admin-chat-grid">
        @foreach($subjects->unique('name') as $subject)
            @php
                $normalizedName = mb_strtolower(
                    trim($subject->name)
                );

                $theme = match ($normalizedName) {
                    'arabe' => [
                        'icon' => 'bi-translate',
                        'eyebrow' => 'Discussion pédagogique',
                        'gradient' =>
                            'linear-gradient(135deg,#0284C7,#2563EB)',
                        'soft' => 'rgba(2,132,199,0.13)',
                        'accent' => '#38BDF8',
                        'description' =>
                            'Échangez autour des cours, exercices '
                            . 'et parcours de langue arabe.',
                    ],

                    'coran' => [
                        'icon' => 'bi-book-half',
                        'eyebrow' => 'Discussion religieuse',
                        'gradient' =>
                            'linear-gradient(135deg,#7C3AED,#A855F7)',
                        'soft' => 'rgba(124,58,237,0.13)',
                        'accent' => '#C084FC',
                        'description' =>
                            'Centralisez les échanges concernant '
                            . 'l’apprentissage et le Tajwid.',
                    ],

                    'administration' => [
                        'icon' => 'bi-shield-lock-fill',
                        'eyebrow' => 'Conversations privées',
                        'gradient' =>
                            'linear-gradient(135deg,#059669,#10B981)',
                        'soft' => 'rgba(16,185,129,0.12)',
                        'accent' => '#34D399',
                        'description' =>
                            'Communiquez individuellement avec les '
                            . 'étudiants et les professeurs.',
                    ],

                    default => [
                        'icon' => 'bi-chat-square-dots-fill',
                        'eyebrow' => 'Discussion',
                        'gradient' =>
                            'linear-gradient(135deg,#2563EB,#4F46E5)',
                        'soft' => 'rgba(37,99,235,0.12)',
                        'accent' => '#60A5FA',
                        'description' =>
                            'Consultez et gérez les échanges.',
                    ],
                };

                $lastMessage =
                    $subject->messages->first();

                $messageCount =
                    (int) $subject->messages_count;
            @endphp

            <article
                class="admin-chat-card"
                style="
                    --chat-gradient:
                        {{ $theme['gradient'] }};
                    --chat-soft:
                        {{ $theme['soft'] }};
                    --chat-accent:
                        {{ $theme['accent'] }};
                "
            >
                <div class="admin-chat-card-cover">
                    <div class="admin-chat-cover-orb orb-one"></div>
                    <div class="admin-chat-cover-orb orb-two"></div>

                    <div class="admin-chat-main-icon">
                        <i class="bi {{ $theme['icon'] }}"></i>
                    </div>

                    @if($messageCount > 0)
                        <span class="admin-chat-live-badge">
                            <span></span>
                            Actif
                        </span>
                    @endif
                </div>

                <div class="admin-chat-card-body">
                    <div class="admin-chat-eyebrow">
                        {{ $theme['eyebrow'] }}
                    </div>

                    <h2>{{ $subject->name }}</h2>

                    <p class="admin-chat-description">
                        {{ $theme['description'] }}
                    </p>

                    <div class="admin-chat-message-summary">
                        <span class="summary-icon">
                            <i class="bi bi-chat-left-text-fill"></i>
                        </span>

                        <div>
                            <strong>
                                {{ $messageCount }}
                                {{
                                    $messageCount > 1
                                        ? 'messages'
                                        : 'message'
                                }}
                            </strong>

                            <span>
                                @if($lastMessage)
                                    {{
                                        \Illuminate\Support\Str::limit(
                                            $lastMessage->message,
                                            52
                                        )
                                    }}
                                @else
                                    Aucune discussion commencée
                                @endif
                            </span>
                        </div>
                    </div>

                    <a
                        href="{{
                            route(
                                'admin.chat',
                                $subject->id
                            )
                        }}"
                        class="admin-chat-action"
                    >
                        <span>
                            <i class="bi bi-box-arrow-in-right"></i>

                            {{
                                $messageCount > 0
                                    ? 'Voir les discussions'
                                    : 'Ouvrir le chat'
                            }}
                        </span>

                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="adm-card">
        <div class="adm-empty admin-chat-empty">
            <div class="adm-empty-icon">
                <i class="bi bi-chat-square-dots"></i>
            </div>

            <h5>Aucun espace de discussion</h5>

            <p>
                Les matières de chat ne sont pas encore disponibles.
            </p>

            <button
                type="button"
                class="adm-btn adm-btn-primary"
                onclick="window.location.reload()"
            >
                <i class="bi bi-arrow-clockwise"></i>
                Actualiser
            </button>
        </div>
    </div>
@endif

<style>
.chat-page-header {
    align-items: center;
}

.chat-page-header h1 {
    display: flex;
    align-items: center;
    gap: 11px;
}

.chat-page-icon {
    width: 43px;
    height: 43px;
    display: inline-grid;
    place-items: center;
    border: 1px solid rgba(96,165,250,0.17);
    border-radius: 13px;
    color: #60A5FA;
    background: rgba(37,99,235,0.11);
    font-size: 1.1rem;
}

.chat-header-statistics {
    display: grid;
    grid-template-columns: repeat(3, auto);
    gap: 10px;
}

.chat-header-statistics > div {
    min-width: 92px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    padding: 10px 13px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 13px;
    background: rgba(255,255,255,0.025);
}

.chat-header-statistics strong {
    color: rgba(255,255,255,0.92);
    font-size: 1.05rem;
    line-height: 1;
}

.chat-header-statistics span {
    color: var(--adm-text-muted);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.055em;
    text-transform: uppercase;
}

.admin-chat-grid {
    width: min(100%, 1080px);
    display: grid;
    grid-template-columns:
        repeat(3, minmax(0, 1fr));
    align-items: stretch;
    gap: 20px;
    margin: 1.2rem auto 0;
}

.admin-chat-card {
    min-width: 0;
    min-height: 410px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.065);
    border-radius: 21px;
    background:
        linear-gradient(
            150deg,
            rgba(17,27,47,0.98),
            rgba(9,17,32,0.99)
        );
    box-shadow:
        0 18px 46px rgba(0,0,0,0.24);
    transition:
        transform 0.28s ease,
        border-color 0.28s ease,
        box-shadow 0.28s ease;
}

.admin-chat-card:hover {
    transform: translateY(-6px);
    border-color: var(--chat-accent);
    box-shadow:
        0 26px 58px rgba(0,0,0,0.31);
}

.admin-chat-card-cover {
    position: relative;
    height: 126px;
    flex: 0 0 126px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: var(--chat-gradient);
}

.admin-chat-card-cover::after {
    content: "";
    position: absolute;
    inset: auto 0 0;
    height: 45%;
    background:
        linear-gradient(
            180deg,
            transparent,
            rgba(4,10,22,0.13)
        );
}

.admin-chat-cover-orb {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
}

.admin-chat-cover-orb.orb-one {
    width: 150px;
    height: 150px;
    top: -88px;
    right: -36px;
}

.admin-chat-cover-orb.orb-two {
    width: 95px;
    height: 95px;
    left: -32px;
    bottom: -55px;
}

.admin-chat-main-icon {
    position: relative;
    z-index: 2;
    width: 64px;
    height: 64px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 19px;
    color: #ffffff;
    background: rgba(255,255,255,0.13);
    box-shadow:
        0 12px 28px rgba(0,0,0,0.18);
    backdrop-filter: blur(10px);
    font-size: 1.7rem;
}

.admin-chat-live-badge {
    position: absolute;
    z-index: 3;
    top: 13px;
    right: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 9px;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 999px;
    color: rgba(255,255,255,0.92);
    background: rgba(7,15,30,0.2);
    font-size: 0.61rem;
    font-weight: 800;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
}

.admin-chat-live-badge span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ADE80;
    box-shadow: 0 0 10px rgba(74,222,128,0.9);
}

.admin-chat-card-body {
    min-height: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.25rem;
}

.admin-chat-eyebrow {
    margin-bottom: 0.4rem;
    color: var(--chat-accent);
    font-size: 0.66rem;
    font-weight: 800;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.admin-chat-card h2 {
    min-height: 31px;
    margin: 0 0 0.45rem;
    color: rgba(255,255,255,0.96);
    font-size: 1.18rem;
    font-weight: 820;
}

.admin-chat-description {
    min-height: 64px;
    margin: 0 0 0.95rem;
    color: rgba(255,255,255,0.48);
    font-size: 0.77rem;
    line-height: 1.55;
}

.admin-chat-message-summary {
    min-height: 68px;
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 1rem;
    padding: 10px 11px;
    border: 1px solid rgba(255,255,255,0.055);
    border-radius: 13px;
    background: var(--chat-soft);
}

.admin-chat-message-summary .summary-icon {
    width: 37px;
    height: 37px;
    flex: 0 0 37px;
    display: grid;
    place-items: center;
    border-radius: 11px;
    color: var(--chat-accent);
    background: rgba(255,255,255,0.045);
}

.admin-chat-message-summary > div {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.admin-chat-message-summary strong {
    color: rgba(255,255,255,0.9);
    font-size: 0.78rem;
}

.admin-chat-message-summary span {
    overflow: hidden;
    color: rgba(255,255,255,0.43);
    font-size: 0.68rem;
    line-height: 1.35;
    text-overflow: ellipsis;
}

.admin-chat-action {
    min-height: 43px;
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 13px;
    padding: 10px 14px;
    border-radius: 12px;
    color: #ffffff;
    background: var(--chat-gradient);
    box-shadow:
        0 10px 25px rgba(0,0,0,0.14);
    font-size: 0.77rem;
    font-weight: 780;
    text-decoration: none;
    transition:
        transform 0.22s ease,
        filter 0.22s ease;
}

.admin-chat-action span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.admin-chat-action:hover {
    color: #ffffff;
    filter: brightness(1.06);
    transform: translateY(-2px);
}

.admin-chat-action > i {
    transition: transform 0.22s ease;
}

.admin-chat-action:hover > i {
    transform: translateX(4px);
}

.admin-chat-empty {
    padding: 4rem 2rem;
}

@media (max-width: 980px) {
    .chat-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .admin-chat-grid {
        width: min(100%, 720px);
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 650px) {
    .chat-header-statistics {
        width: 100%;
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }

    .chat-header-statistics > div {
        min-width: 0;
    }

    .admin-chat-grid {
        width: min(100%, 420px);
        grid-template-columns: 1fr;
    }

    .admin-chat-card {
        min-height: 385px;
    }
}
</style>

@endsection
