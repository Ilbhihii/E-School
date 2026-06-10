@extends('layouts.front')

@section('content')
<div class="st-page">
  <div class="st-container">
    <div class="row justify-content-center">
      <div class="col-md-8">

        <div class="st-card st-fade-up">
          <div class="st-card-body text-center" style="padding: 3rem;">

            <div class="st-mb-4">
              <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--st-warning);"></i>
            </div>

            <h2 style="font-weight: 700; margin-bottom: 1rem;">Test complété avec succès !</h2>

            @if (session('success'))
              @php
                preg_match('/Score:\s*(\d+)\/(\d+)/', session('success'), $matches);
                $score = $matches[1] ?? 0;
                $total = $matches[2] ?? 0;
                $percentage = $total > 0 ? round(($score / $total) * 100, 1) : 0;
              @endphp
              <div style="background: linear-gradient(135deg, #16a34a, #0d9488); color: white; border-radius: 24px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(22,163,74,0.3);">
                <h3 style="font-weight: 800; margin-bottom: .75rem;">Votre Score</h3>
                <div style="font-size: 3rem; font-weight: 900; margin-bottom: .25rem;">{{ $score }} / {{ $total }}</div>
                <div style="font-size: 1.2rem; font-weight: 700; opacity: .9;">{{ $percentage }}%</div>
                <div style="height: 10px; background: rgba(255,255,255,0.3); border-radius: 999px; margin-top: 1rem; overflow: hidden;">
                  <div style="height: 100%; width: {{ $percentage }}%; background: white; border-radius: 999px; transition: width 1s;"></div>
                </div>
              </div>
              <div class="st-alert st-alert-success">{{ session('success') }}</div>
            @endif

            <p style="font-size: 1.1rem; color: var(--st-text-light); margin-bottom: 1.5rem;">
              Votre test a été enregistré. Un administrateur va examiner vos résultats et activer votre compte sous peu.
            </p>

            <div class="st-alert st-alert-info">
              <i class="bi bi-info-circle"></i> Vous serez notifié par email dès que votre compte sera activé.
            </div>

            <div class="st-mt-4">
              @if(auth()->user()->is_active)
                <a href="{{ route('plans') }}" class="st-btn st-btn-success st-btn-lg">
                  Aller aux plans
                </a>
              @else
                <a href="{{ route('home') }}" class="st-btn st-btn-primary st-btn-lg">
                  <i class="bi bi-house me-1"></i> Retour à l'accueil
                </a>
              @endif
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
