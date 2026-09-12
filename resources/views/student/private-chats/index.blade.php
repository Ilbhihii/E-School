@extends('layouts.student')

@section('title', 'Messages privés')
@section('page_title', 'Messages privés')
@section('breadcrumb', 'Communication → Messages privés')

@section('content')

<style>
.private-chat-page {
    --pc-border: rgba(255,255,255,.07);
    --pc-muted: rgba(255,255,255,.52);
    --pc-panel: rgba(13,22,45,.92);
    --pc-panel-2: rgba(17,28,56,.92);
    --pc-purple: #8b5cf6;
    --pc-blue: #38bdf8;
    --pc-green: #34d399;
}

.pc-hero {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
    margin-bottom:1rem;
    padding:1.35rem 1.45rem;
    border:1px solid var(--pc-border);
    border-radius:20px;
    background:
        radial-gradient(circle at 100% 0%, rgba(56,189,248,.10), transparent 34%),
        linear-gradient(135deg, rgba(20,30,62,.96), rgba(12,21,44,.96));
}

.pc-hero-main {
    display:flex;
    align-items:center;
    gap:1rem;
}

.pc-hero-icon {
    width:52px;
    height:52px;
    display:grid;
    place-items:center;
    flex:0 0 52px;
    border-radius:15px;
    color:#bae6fd;
    background:rgba(56,189,248,.12);
    border:1px solid rgba(56,189,248,.18);
    font-size:1.2rem;
}

.pc-kicker {
    display:block;
    margin-bottom:.2rem;
    color:#93c5fd;
    font-size:.65rem;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
}

.pc-hero h1 {
    margin:0;
    color:#fff;
    font-size:1.45rem;
    font-weight:900;
}

.pc-hero p {
    margin:.35rem 0 0;
    color:var(--pc-muted);
    font-size:.82rem;
}

.pc-back {
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    color:#c4b5fd;
    text-decoration:none;
    font-size:.75rem;
    font-weight:800;
}

.pc-search {
    position:relative;
    display:block;
    margin-bottom:1rem;
}

.pc-search i {
    position:absolute;
    left:1rem;
    top:50%;
    transform:translateY(-50%);
    color:rgba(255,255,255,.35);
}

.pc-search input {
    width:100%;
    min-height:46px;
    padding:.7rem 1rem .7rem 2.7rem;
    border:1px solid var(--pc-border);
    border-radius:13px;
    outline:none;
    color:#fff;
    background:rgba(10,18,38,.9);
}

.pc-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:1rem;
}

.pc-contact {
    display:block;
    padding:1rem 1.05rem;
    border:1px solid var(--pc-border);
    border-radius:17px;
    text-decoration:none;
    background:
        radial-gradient(circle at 100% 0%, rgba(139,92,246,.08), transparent 38%),
        var(--pc-panel);
    transition:.2s ease;
}

.pc-contact:hover {
    transform:translateY(-2px);
    border-color:rgba(56,189,248,.24);
    box-shadow:0 14px 30px rgba(0,0,0,.16);
}

.pc-contact-top {
    display:flex;
    align-items:center;
    gap:.8rem;
}

.pc-avatar {
    width:44px;
    height:44px;
    display:grid;
    place-items:center;
    flex:0 0 44px;
    border-radius:14px;
    color:#fff;
    background:linear-gradient(135deg,#7c3aed,#0ea5e9);
    font-size:.9rem;
    font-weight:900;
}

.pc-contact-copy {
    min-width:0;
    flex:1;
}

.pc-contact-copy h3 {
    margin:0;
    color:#fff;
    font-size:.9rem;
    font-weight:850;
}

.pc-contact-copy p {
    margin:.22rem 0 0;
    color:var(--pc-muted);
    font-size:.7rem;
}

.pc-path {
    display:flex;
    flex-wrap:wrap;
    gap:.4rem;
    margin-top:.85rem;
}

.pc-chip {
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    padding:.35rem .55rem;
    border-radius:999px;
    color:#cbd5e1;
    background:rgba(255,255,255,.035);
    border:1px solid rgba(255,255,255,.055);
    font-size:.62rem;
    font-weight:750;
}

.pc-last {
    margin-top:.85rem;
    padding-top:.75rem;
    border-top:1px solid rgba(255,255,255,.055);
    color:rgba(255,255,255,.46);
    font-size:.68rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.pc-empty {
    grid-column:1/-1;
    padding:3rem 1rem;
    border:1px dashed rgba(255,255,255,.10);
    border-radius:17px;
    text-align:center;
    color:rgba(255,255,255,.48);
    background:rgba(255,255,255,.015);
}

@media(max-width:850px) {
    .pc-grid { grid-template-columns:1fr; }
    .pc-hero { align-items:flex-start; flex-direction:column; }
}
</style>

@php
    $contacts = $contacts ?? collect();

    $initials = function ($name) {
        return collect(
            preg_split('/\s+/u', trim($name))
        )
            ->filter()
            ->take(2)
            ->map(
                fn ($part) =>
                    mb_strtoupper(
                        mb_substr($part, 0, 1)
                    )
            )
            ->implode('');
    };
@endphp

<div class="private-chat-page">
    <section class="pc-hero">
        <div class="pc-hero-main">
            <span class="pc-hero-icon">
                <i class="bi bi-person-lock"></i>
            </span>

            <div>
                <span class="pc-kicker">
                    Conversation individuelle
                </span>

                <h1>Messages privés</h1>

                <p>
                    Échangez individuellement avec les professeurs
                    affectés à votre parcours.
                </p>
            </div>
        </div>

        <a
            href="{{ route('student.chats') }}"
            class="pc-back"
        >
            <i class="bi bi-arrow-left"></i>
            Discussions
        </a>
    </section>

    @if($contacts->count() > 4)
        <label class="pc-search">
            <i class="bi bi-search"></i>

            <input
                type="search"
                placeholder="Rechercher un professeur..."
                id="privateContactSearch"
            >
        </label>
    @endif

    <div class="pc-grid" id="privateContactGrid">
        @forelse($contacts as $contact)
            <a
                href="{{
                    route(
                        'student.private-chats.show',
                        [
                            'professor' =>
                                $contact->professor_id,
                            'subject' =>
                                $contact->subject_id,
                        ]
                    )
                }}"
                class="pc-contact"
                data-contact="{{
                    mb_strtolower(
                        $contact->professor_name
                        . ' '
                        . $contact->subject_name
                        . ' '
                        . $contact->level_name
                        . ' '
                        . $contact->class_name
                    )
                }}"
            >
                <div class="pc-contact-top">
                    <span class="pc-avatar">
                        {{
                            $initials(
                                $contact->professor_name
                            ) ?: 'E'
                        }}
                    </span>

                    <div class="pc-contact-copy">
                        <h3>
                            {{ $contact->professor_name }}
                        </h3>

                        <p>
                            Professeur · {{ $contact->subject_name }}
                        </p>
                    </div>

                    <i
                        class="bi bi-chevron-right"
                        style="color:rgba(255,255,255,.28);"
                    ></i>
                </div>

                <div class="pc-path">
                    <span class="pc-chip">
                        <i class="bi bi-book"></i>
                        {{ $contact->subject_name }}
                    </span>

                    <span class="pc-chip">
                        {{ $contact->level_name }}
                    </span>

                    <span class="pc-chip">
                        {{ $contact->class_name }}
                    </span>
                </div>

                <div class="pc-last">
                    @if($contact->last_message)
                        <i class="bi bi-chat-left-text me-1"></i>
                        {{
                            \Illuminate\Support\Str::limit(
                                $contact
                                    ->last_message
                                    ->message,
                                70
                            )
                        }}
                    @else
                        <i class="bi bi-chat-dots me-1"></i>
                        Aucune conversation pour le moment.
                    @endif
                </div>
            </a>
        @empty
            <div class="pc-empty">
                <i class="bi bi-person-x d-block mb-2"></i>
                Aucun professeur n'est actuellement
                affecté à votre parcours.
            </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {
        const search =
            document.getElementById(
                'privateContactSearch'
            );

        if (!search) {
            return;
        }

        search.addEventListener(
            'input',
            function () {
                const term =
                    search.value
                        .toLowerCase()
                        .trim();

                document
                    .querySelectorAll(
                        '#privateContactGrid [data-contact]'
                    )
                    .forEach(function (card) {
                        card.style.display =
                            card.dataset.contact.includes(
                                term
                            )
                                ? ''
                                : 'none';
                    });
            }
        );
    }
);
</script>
@endsection
