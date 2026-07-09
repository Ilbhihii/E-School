@extends('layouts.front')

@section('title', $course->title)

@section('content')

<style>
/* ── Light mode fixes for course-show ── */
html.light-mode .course-section-title {
    color: #1e293b !important;
}
html.light-mode .course-text-muted {
    color: #64748b !important;
}
html.light-mode .course-card {
    background: rgba(255,255,255,0.85) !important;
    border-color: rgba(0,0,0,0.08) !important;
}
html.light-mode .course-card h5 {
    color: #1e293b !important;
}
html.light-mode .course-card p {
    color: #64748b !important;
}
html.light-mode .course-meta-item {
    background: rgba(0,0,0,0.03) !important;
    border-color: rgba(0,0,0,0.06) !important;
}
html.light-mode .course-meta-item span:first-child {
    color: #94a3b8 !important;
}
html.light-mode .course-meta-item span:last-child {
    color: #1e293b !important;
}
html.light-mode .subject-link-card {
    background: rgba(255,255,255,0.85) !important;
    border-color: rgba(0,0,0,0.08) !important;
}
html.light-mode .subject-link-card h6 {
    color: #1e293b !important;
}
html.light-mode .subject-link-card small {
    color: #94a3b8 !important;
}
html.light-mode .course-description p {
    color: #64748b !important;
}
</style>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Breadcrumb amélioré -->
                <nav style="display:flex;align-items:center;gap:8px;margin-bottom:1.5rem;font-size:0.82rem;color:rgba(255,255,255,0.35);flex-wrap:wrap;">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        <i class="bi bi-house me-1"></i>Accueil
                    </a>
                    <span style="color:rgba(255,255,255,0.12);">/</span>

                    @if($course->subject)
                    <a href="{{ route('front.subject.levels', $course->subject->id) }}" style="color:rgba(255,255,255,0.4);text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.7)'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                        {{ $course->subject->name }}
                    </a>
                    <span style="color:rgba(255,255,255,0.12);">/</span>
                    @endif

                    @if($course->level)
                    <span style="color:rgba(255,255,255,0.5);">{{ $course->level->name }}</span>
                    <span style="color:rgba(255,255,255,0.12);">/</span>
                    @endif

                    <span style="color:rgba(255,255,255,0.6);font-weight:500;">{{ Str::limit($course->title, 40) }}</span>
                </nav>

                <!-- Précédent -->
                <a href="{{ url()->previous() }}" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem; transition: color 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;"
   onmouseover="this.style.color='rgba(255,209,102,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
    <i class="bi bi-arrow-left"></i> Précédent
</a>

                <!-- Title -->
                <h2 class="section-title-3d mb-3" style="font-size: 2rem;">{{ $course->title }}</h2>

                <!-- Badges info -->
                <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:1.5rem;">
                    @if($course->subject)
                    <span style="padding:5px 14px;border-radius:20px;background:rgba(124,58,237,0.12);color:#A78BFA;font-size:0.75rem;font-weight:500;border:1px solid rgba(124,58,237,0.15);">
                        <i class="bi bi-book me-1"></i>{{ $course->subject->name }}
                    </span>
                    @endif
                    @if($course->level)
                    <span style="padding:5px 14px;border-radius:20px;background:rgba(2,132,199,0.12);color:#38BDF8;font-size:0.75rem;font-weight:500;border:1px solid rgba(2,132,199,0.15);">
                        <i class="bi bi-layers me-1"></i>{{ $course->level->name }}
                    </span>
                    @endif
                    @if($course->classRoom)
                    <span style="padding:5px 14px;border-radius:20px;background:rgba(34,197,94,0.12);color:#4ADE80;font-size:0.75rem;font-weight:500;border:1px solid rgba(34,197,94,0.15);">
                        <i class="bi bi-building me-1"></i>{{ $course->classRoom->name }}
                    </span>
                    @endif
                    <span style="padding:5px 14px;border-radius:20px;background:rgba(255,209,102,0.12);color:#FFD166;font-size:0.75rem;font-weight:500;border:1px solid rgba(255,209,102,0.15);">
                        <i class="bi bi-play-circle me-1"></i>{{ $course->learningTests->count() > 0 ? $course->learningTests->count() . ' question(s)' : 'Cours' }}
                    </span>
                </div>

                <!-- Premium alert -->
                @if(!$course->is_free)
                    <div class="alert mb-4" style="background: rgba(255,209,102,0.12); color: #FFD166; border: 1px solid rgba(255,209,102,0.2); border-radius: 12px;">
                        <i class="bi bi-lock-fill me-2"></i> Contenu Premium — Abonnez-vous pour accéder à ce cours
                    </div>
                @endif

                <!-- Video -->
                @if($course->video_url)
                <div class="card-3d overflow-hidden p-0 mb-4" style="border-radius: 20px;">
                    <div style="position: relative; padding-bottom: 56.25%; height: 0;">
                        <iframe src="{{ $course->video_url }}" frameborder="0" allowfullscreen
                                style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;"></iframe>
                    </div>
                </div>
                @endif

                <!-- Publicité / Explication du contenu -->
                <div class="card-3d mb-4 course-card">
                    <div class="p-4">
                        <h5 class="fw-bold mb-3 course-section-title" style="font-family: 'Poppins', sans-serif;color:rgba(255,255,255,0.9);">
                            <i class="bi bi-info-circle me-2" style="color: rgba(255,255,255,0.4);"></i>À propos de ce cours
                        </h5>
                        <div class="course-description">
                            <p style="color: rgba(255,255,255,0.6); line-height: 1.8;" class="course-text-muted">
                                {{ $course->description ?? 'Aucune description disponible pour ce cours.' }}
                            </p>
                        </div>

                        @if($course->subject)
                        <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid rgba(255,255,255,0.04);">
                            <div style="display:flex;flex-wrap:wrap;gap:12px;">
                                <div class="course-meta-item" style="flex:1;min-width:140px;padding:12px 16px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);">
                                    <span style="display:block;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em;color:#64748B;font-weight:600;">Matière</span>
                                    <span style="display:block;font-weight:600;font-size:0.9rem;color:#F1F5F9;margin-top:2px;">{{ $course->subject->name }}</span>
                                </div>
                                @if($course->level)
                                <div class="course-meta-item" style="flex:1;min-width:140px;padding:12px 16px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);">
                                    <span style="display:block;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em;color:#64748B;font-weight:600;">Niveau</span>
                                    <span style="display:block;font-weight:600;font-size:0.9rem;color:#F1F5F9;margin-top:2px;">{{ $course->level->name }}</span>
                                </div>
                                @endif
                                @if($course->learningTests->count() > 0)
                                <div class="course-meta-item" style="flex:1;min-width:140px;padding:12px 16px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);">
                                    <span style="display:block;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em;color:#64748B;font-weight:600;">Test</span>
                                    <span style="display:block;font-weight:600;font-size:0.9rem;color:#F1F5F9;margin-top:2px;">{{ $course->learningTests->count() }} question(s)</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Proposer de passer le test -->
                @if($course->learningTests && $course->learningTests->count() > 0)
                <div class="card-3d mb-4 course-card" style="border-color:rgba(124,58,237,0.1);">
                    <div class="p-4 text-center">
                        <div style="width:64px;height:64px;border-radius:16px;background:rgba(124,58,237,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                            <i class="bi bi-pencil-square" style="font-size:1.5rem;color:#A78BFA;"></i>
                        </div>
                        <h5 class="fw-bold course-section-title" style="font-family: 'Poppins', sans-serif;color:rgba(255,255,255,0.9);">Testez vos connaissances</h5>
                        <p style="color:rgba(255,255,255,0.5);max-width:400px;margin:0 auto 1.25rem;" class="course-text-muted">
                            Ce cours contient <strong style="color:#A78BFA;">{{ $course->learningTests->count() }} question(s)</strong> pour évaluer votre compréhension.
                        </p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            @auth
                                <a href="#" class="btn-3d btn-3d-gradient">
                                    <i class="bi bi-pencil-square"></i> Passer le test
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-3d btn-3d-gradient">
                                    <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour passer le test
                                </a>
                                <a href="{{ route('register') }}" class="btn-3d btn-3d-outline">
                                    <i class="bi bi-person-plus"></i> Créer un compte gratuit
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                @else
                <div class="d-flex flex-wrap gap-3 mb-4">
                    @auth
                        <a href="#" class="btn-3d btn-3d-gradient">
                            <i class="bi bi-pencil-square"></i> Passer le test
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-3d btn-3d-outline">
                            <i class="bi bi-box-arrow-in-right"></i> Connectez-vous pour passer le test
                        </a>
                    @endauth
                </div>
                @endif

                <!-- Autres matières de la même famille -->
                @if($sameFamilySubjects && $sameFamilySubjects->count() > 0)
                <div class="mt-5">
                    <h5 class="fw-bold mb-3 course-section-title" style="font-family: 'Poppins', sans-serif;color:rgba(255,255,255,0.9);">
                        <i class="bi bi-collection me-2" style="color:rgba(255,255,255,0.3);"></i>
                        Autres matières
                        @if($course->subject)
                        <span style="font-size:0.8rem;font-weight:400;color:rgba(255,255,255,0.3);">de la même catégorie</span>
                        @endif
                    </h5>
                    <div class="row g-3">
                        @foreach($sameFamilySubjects as $relatedSubject)
                        <div class="col-md-6">
                            <a href="{{ route('front.subject.levels', $relatedSubject->id) }}" style="text-decoration:none;display:block;">
                                <div class="subject-link-card card-3d p-3 d-flex align-items-center gap-3" style="transition:all 0.2s ease;">
                                    <div style="width:44px;height:44px;border-radius:10px;background:rgba(124,58,237,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-book" style="color:#A78BFA;"></i>
                                    </div>
                                    <div style="min-width:0;flex:1;">
                                        <h6 style="font-weight:600;color:#F1F5F9;margin:0;font-size:0.85rem;">{{ $relatedSubject->name }}</h6>
                                        <small style="color:#64748B;font-size:0.7rem;">{{ $relatedSubject->courses_count ?? 0 }} cours disponible(s)</small>
                                    </div>
                                    <i class="bi bi-chevron-right" style="color:rgba(255,255,255,0.2);font-size:0.75rem;"></i>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

@endsection
