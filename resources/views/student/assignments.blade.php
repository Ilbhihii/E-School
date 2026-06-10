@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    @if(session('success'))
      <div class="st-alert st-alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="st-hero st-hero-green st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-clipboard-check me-2"></i>Mes Devoirs</h1>
        <p>Envoyer & suivre vos devoirs</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-clipboard-check-fill"></i>
      </div>
    </div>

    {{-- Upload form --}}
    <div class="st-card st-fade-up st-fade-up-d1">
      <div class="st-card-header">
        <i class="bi bi-cloud-upload-fill" style="color: var(--st-success);"></i>
        <h5>Envoyer un nouveau devoir</h5>
      </div>
      <div class="st-card-body">
        <form method="POST" action="{{ route('student.assignments.send') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <div class="st-form-group">
                <label class="st-form-label"><i class="bi bi-book-half me-1 text-success"></i>Cours <span class="text-danger">*</span></label>
                <select name="course_id" class="st-form-select" required>
                  <option value="">Sélectionner un cours...</option>
                  @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title ?? $course->name }} ({{ $course->classRoom->name ?? 'Classe' }})</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="st-form-group">
                <label class="st-form-label"><i class="bi bi-journal-text me-1 text-primary"></i>Titre du devoir <span class="text-danger">*</span></label>
                <input type="text" name="title" class="st-form-input" placeholder="Ex: Devoir de mathématiques - Chapitre 3" required>
              </div>
            </div>
            <div class="col-12">
              <div class="st-form-group">
                <label class="st-form-label"><i class="bi bi-file-earmark-pdf me-1 text-danger"></i>Fichier (PDF / Word)</label>
                <div class="st-upload-zone">
                  <i class="bi bi-cloud-arrow-up-fill" style="font-size: 1.5rem; color: var(--st-primary); display: block; margin-bottom: .5rem;"></i>
                  <p class="st-mb-1" style="font-weight: 600; color: var(--st-text-light);">Glisser-déposer ou cliquer</p>
                  <small style="color: var(--st-text-light);">PDF, DOC, DOCX (Max 10MB)</small>
                  <input type="file" name="file" class="st-file-input st-mt-2" accept=".pdf,.doc,.docx" required>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center st-mt-3">
            <button type="submit" class="st-btn st-btn-success st-btn-lg">
              <i class="bi bi-upload me-1"></i> Envoyer le devoir
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Assignments list --}}
    <div class="st-card st-mt-4 st-fade-up st-fade-up-d2">
      <div class="st-card-header">
        <i class="bi bi-list-check" style="color: var(--st-success);"></i>
        <h5>Mes devoirs envoyés</h5>
        <span class="st-badge st-badge-primary" style="margin-left: auto;">{{ $assignments->count() }} devoir{{ $assignments->count() > 1 ? 's' : '' }}</span>
      </div>
      <div class="st-card-body">

        @if($assignments->count() === 0)
          <div class="st-empty">
            <i class="bi bi-inbox"></i>
            <h5>Aucun devoir envoyé</h5>
            <p>Commencez par envoyer votre premier devoir ci-dessus !</p>
          </div>
        @else
          <div class="st-table-wrap">
            <table class="st-table">
              <thead>
                <tr>
                  <th>Devoir</th>
                  <th>Fichier</th>
                  <th class="text-center">Statut</th>
                  <th>Commentaire</th>
                </tr>
              </thead>
              <tbody>
                @foreach($assignments as $a)
                  <tr>
                    <td>
                      <div style="font-weight: 600;">{{ Str::limit($a->title, 40) }}</div>
                      <small style="color: var(--st-text-light);">{{ $a->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                      <a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="st-btn st-btn-outline st-btn-sm">
                        <i class="bi bi-eye me-1"></i> Voir
                      </a>
                    </td>
                    <td class="text-center">
                      @if($a->grade)
                        <span class="st-badge st-badge-success"><i class="bi bi-check-circle-fill me-1"></i>Corrigé</span>
                      @else
                        <span class="st-badge st-badge-warning"><i class="bi bi-hourglass me-1"></i>En attente</span>
                      @endif
                    </td>
                    <td>
                      @if($a->comment)
                        <small style="color: var(--st-text-light);">{{ Str::limit($a->comment, 60) }}</small>
                      @else
                        <small style="color: var(--st-muted);">-</small>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

      </div>
    </div>

  </div>
</div>
@endsection
