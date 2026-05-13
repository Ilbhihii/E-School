@extends('layouts.student')

@section('content')
<style>
/* Course Page Modern Design - Student Theme */
:root {
  --primary-green: #16a34a;
  --gradient-green: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
  --glass-bg: rgba(255, 255, 255, 0.9);
  --glass-border: rgba(255, 255, 255, 0.3);
  --card-shadow: 0 20px 60px rgba(0,0,0,0.1);
  --hover-shadow: 0 30px 80px rgba(0,0,0,0.15);
}

.course-hero {
  background: var(--gradient-green), linear-gradient(135deg, rgba(22,163,74,0.1) 0%, rgba(34,197,94,0.05) 100%);
  border-radius: 32px 32px 0 0;
  padding: 3rem 2rem;
  position: relative;
  overflow: hidden;
  margin-bottom: -2rem;
}

.course-hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.3) 0%, transparent 50%);
  z-index: 0;
}

.course-hero-content {
  position: relative;
  z-index: 1;
}

.course-title-gradient {
  background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,0.9) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-weight: 800;
}

.glass-card {
  background: var(--glass-bg);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: 24px;
  box-shadow: var(--card-shadow);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.glass-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-green);
}

.glass-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--hover-shadow);
}

.section-header {
  background: var(--gradient-green);
  color: white;
  font-weight: 700;
  padding: 1.25rem 2rem;
  border-radius: 20px 20px 0 0;
  position: relative;
  overflow: hidden;
}

.section-header::after {
  content: '';
  position: absolute;
  top: 50%;
  right: 2rem;
  width: 40px;
  height: 40px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  transform: translateY(-50%);
}

.custom-video-wrapper {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(16, 175, 129, 0.2);
}

.custom-video-wrapper video {
  width: 100%;
  height: auto;
  border-radius: 20px;
}

.gradient-btn {
  background: linear-gradient(135deg, var(--primary-green) 0%, #22c55e 100%);
  border: none;
  border-radius: 50px;
  padding: 0.875rem 2.5rem;
  font-weight: 700;
  color: white;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 10px 30px rgba(22, 163, 74, 0.4);
  position: relative;
  overflow: hidden;
}

.gradient-btn:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 20px 40px rgba(22, 163, 74, 0.6);
  color: white;
  text-decoration: none;
}

.gradient-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  transition: left 0.6s;
}

.gradient-btn:hover::before {
  left: 100%;
}

.btn-outline-gradient {
  border: 2px solid transparent;
  background: linear-gradient(white, white) padding-box, var(--gradient-green) border-box;
  color: var(--primary-green);
  font-weight: 700;
}

.btn-outline-gradient:hover {
  background: var(--gradient-green);
  color: white;
  transform: translateY(-2px);
}

.info-card {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(229,231,235,0.5);
  border-radius: 24px;
  box-shadow: 
    10px 10px 20px rgba(0,0,0,0.05),
    -10px -10px 20px rgba(255,255,255,0.8);
  transition: all 0.3s ease;
}

.info-card:hover {
  box-shadow: 
    5px 5px 15px rgba(0,0,0,0.1),
    -5px -5px 15px rgba(255,255,255,0.9);
}

.subject-badge {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.875rem;
}

.class-badge {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.875rem;
}

.content-available {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin: 1.5rem 0;
}

.content-badge {
  padding: 0.375rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.badge-video { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.badge-pdf { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.badge-link { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; }

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.glass-card { animation: fadeInUp 0.6s ease forwards; }
.info-card { animation: fadeInUp 0.8s ease forwards; }

/* Mobile Responsive */
@media (max-width: 768px) {
  .course-hero { border-radius: 24px; margin-bottom: -1rem; padding: 2rem 1.5rem; }
  .glass-card, .info-card { border-radius: 20px; margin-bottom: 1.5rem; }
  .section-header { padding: 1rem 1.5rem; font-size: 1.1rem; }
}
</style>

<div class="container py-4 px-lg-5">
  
  <!-- HERO BANNER -->
  <div class="course-hero text-center text-white mb-5">
    <div class="course-hero-content">
      <h1 class="display-4 fw-bold mb-2 course-title-gradient">{{ $course->title }}</h1>
      @if($course->description)
        <p class="lead fs-4 opacity-95 mb-0">{{ $course->description }}</p>
      @endif
    </div>
  </div>

  <div class="row g-5">
    
    <!-- MAIN CONTENT -->
    <div class="col-lg-8">
      
      <!-- CONTENT OVERVIEW -->
      <div class="content-available mb-4">
        @if($course->video)
          <span class="content-badge badge-video">🎥 Vidéo</span>
        @endif
        @if($course->pdf)
          <span class="content-badge badge-pdf">📄 PDF</span>
        @endif
        @if($course->course_link)
          <span class="content-badge badge-link">🔗 Lien externe</span>
        @endif
      </div>

      <!-- VIDEO SECTION -->
      @if($course->video)
        <div class="glass-card mb-4">
          <div class="section-header">
            <i class="fas fa-video me-2"></i>🎥 Vidéo du cours
          </div>
          <div class="p-4">
            <div class="custom-video-wrapper mb-4">
              <video class="w-100" controls preload="metadata">
<source src="{{ asset('storage/videos/' . $course->video) }}" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
              </video>
            </div>
            <div class="text-center">
              <a href="{{ asset('storage/' . $course->video) }}" download class="gradient-btn me-3 mb-3">
                ⬇ <i class="fas fa-download me-1"></i> Télécharger
              </a>
            </div>
          </div>
        </div>
      @endif

      <!-- PDF SECTION -->
      @if($course->pdf)
        <div class="glass-card mb-4">
          <div class="section-header" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <i class="fas fa-file-pdf me-2"></i>📄 PDF du cours
          </div>
          <div class="p-4 text-center">
            <a href="{{ asset('storage/' . $course->pdf) }}" class="btn btn-outline-gradient btn-lg px-5 py-3 mb-3" target="_blank">
              📥 <i class="fas fa-download ms-1"></i> Télécharger PDF
            </a>
          </div>
        </div>
      @endif

      <!-- EXTERNAL COURSE LINK -->
      @if($course->course_link)
        <div class="glass-card">
          <div class="section-header" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <i class="fas fa-external-link-alt me-2"></i>🚀 Accéder au cours complet
          </div>
          <div class="p-4 text-center">
            <a href="{{ $course->course_link }}" class="gradient-btn btn-lg px-5 py-3" target="_blank">
              🎯 Ouvrir la plateforme <i class="fas fa-arrow-up-right-from-square ms-2"></i>
            </a>
          </div>
        </div>
      @endif

    </div>

    <!-- INFO SIDEBAR -->
    <div class="col-lg-4">
      <div class="info-card sticky-top" style="top: 2rem;">
        <div class="p-4 pb-3">
          <h5 class="fw-bold mb-4 text-dark text-center">📋 Informations rapides</h5>
          
          @if($course->subject->name ?? false)
            <div class="mb-3">
              <label class="form-label fw-semibold text-muted small mb-2 d-block">📚 Matière</label>
              <span class="subject-badge d-block text-center">{{ $course->subject->name }}</span>
            </div>
          @endif
          
          @if($course->classRoom->name ?? false)
            <div class="mb-4">
              <label class="form-label fw-semibold text-muted small mb-2 d-block">🏫 Classe</label>
              <span class="class-badge d-block text-center">{{ $course->classRoom->name }}</span>
            </div>
          @endif

          <div class="text-center mt-4 pt-3 border-top">
            <a href="{{ route('student.courses') }}" class="btn btn-outline-secondary btn-sm px-4">
              <i class="bi bi-list-ul me-1"></i> Tous les cours
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

