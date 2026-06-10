@extends('layouts.student')

@section('title', 'Tests disponibles')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-violet st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-pencil-square me-2"></i>Tests disponibles</h1>
        <p>Testez vos connaissances avec nos évaluations</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-pencil-square"></i>
      </div>
    </div>

    @if($tests->count() > 0)
      <div class="st-card st-fade-up st-fade-up-d1">
        <div class="st-table-wrap">
          <table class="st-table">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Matière</th>
                <th>Durée</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($tests as $test)
                <tr>
                  <td style="font-weight: 600;">{{ $test->title }}</td>
                  <td>{{ $test->subject->name }}</td>
                  <td>{{ $test->duration }} min</td>
                  <td>
                    <a href="{{ route('student.tests.show', $test) }}" class="st-btn st-btn-primary st-btn-sm">
                      <i class="bi bi-play-circle me-1"></i> Passer le test
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center" style="padding: 2rem; color: var(--st-text-light);">Aucun test disponible</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="st-card st-fade-up">
        <div class="st-empty">
          <i class="bi bi-pencil"></i>
          <h5>Aucun test disponible</h5>
          <p>Les tests apparaîtront ici dès qu'ils seront publiés</p>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection
