@extends('layouts.student')

@section('content')
<div class="st-page">
  <div class="st-container">

    <div class="st-hero st-hero-red st-fade-up st-flex-between">
      <div>
        <h1><i class="bi bi-calendar-x me-2"></i>Mes Absences</h1>
        <p>Suivez vos absences et leur justification</p>
      </div>
      <div style="font-size: 2.5rem; opacity: .6;">
        <i class="bi bi-calendar-x-fill"></i>
      </div>
    </div>

    {{-- Total --}}
    <div class="st-stat st-mb-4 st-fade-up st-fade-up-d1" style="text-align: center;">
      <div style="font-size: 2.5rem; font-weight: 800; color: var(--st-danger);">{{ $totalAbsences }}</div>
      <small>absence{{ $totalAbsences > 1 ? 's' : '' }}</small>
    </div>

    @if($absences->count() > 0)
      <div class="st-card st-fade-up st-fade-up-d2">
        <div class="st-table-wrap">
          <table class="st-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Statut</th>
                <th>Justifiée</th>
              </tr>
            </thead>
            <tbody>
              @foreach($absences as $absence)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($absence->date)->format('d M Y') }}</td>
                  <td><span class="st-badge st-badge-danger">Absent</span></td>
                  <td>
                    @if($absence->justified)
                      <span class="st-badge st-badge-success">Oui</span>
                    @else
                      <span class="st-badge st-badge-danger">Non</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="st-card st-fade-up st-fade-up-d2">
        <div class="st-empty">
          <i class="bi bi-check-circle text-success" style="color: var(--st-success) !important;"></i>
          <h5>Aucune absence enregistrée</h5>
          <p>Vous êtes toujours présent aux cours !</p>
        </div>
      </div>
    @endif

    {{-- Situation alert --}}
    <div class="st-alert st-alert-{{ $color ?? 'info' }} st-mt-4 st-fade-up st-fade-up-d3">
      <strong>Situation :</strong> Total absences : <strong>{{ $totalAbsences }}</strong> — {{ $situation ?? 'Bonne assiduité' }}
    </div>

  </div>
</div>
@endsection
