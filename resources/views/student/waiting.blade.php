@extends('layouts.front')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">

                    <div class="mb-4">
                        <i class="bi bi-hourglass-split display-1 text-warning"></i>
                    </div>
                    <h2 class="mb-3">Test complété avec succès !</h2>
                    @if (session('success'))
                        @php
                            preg_match('/Score:\s*(\d+)\/(\d+)/', session('success'), $matches);
                            $score = $matches[1] ?? 0;
                            $total = $matches[2] ?? 0;
                            $percentage = $total > 0 ? round(($score / $total) * 100, 1) : 0;
                        @endphp
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-8 rounded-3xl shadow-2xl mb-6 animate-pulse">
                            <h3 class="text-3xl font-black mb-3">Votre Score</h3>
                            <div class="text-5xl font-black mb-2">{{ $score }} / {{ $total }}</div>
                            <div class="text-xl font-bold opacity-95">{{ $percentage }}%</div>
                            <div class="w-full bg-white/30 rounded-full h-3 mt-4 overflow-hidden">
                                <div class="bg-white h-3 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p class="lead mb-4">
                        Votre test a été enregistré. Un administrateur va examiner vos résultats 
                        et activer votre compte sous peu.
                    </p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Vous serez notifié par email dès que votre compte sera activé.
                    </div>
                    @if(auth()->user()->is_active)
                        <a href="{{ route('plans') }}" class="btn btn-success">
                            Aller aux plans
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            Retour à l'accueil
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

