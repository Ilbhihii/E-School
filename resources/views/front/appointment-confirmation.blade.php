@extends('layouts.front')

@section('title', 'Rendez-vous envoyé')

@section('content')
<section class="py-5">
    <div class="container" style="max-width:720px;">
        <div class="card-3d p-4 p-md-5 text-center">
            <div class="mx-auto mb-4" style="width:76px;height:76px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(34,197,94,.14);border:1px solid rgba(34,197,94,.25);color:#4ADE80;font-size:2rem;">
                <i class="bi bi-check-lg"></i>
            </div>

            <span class="badge px-3 py-2 mb-3" style="background:rgba(124,58,237,.15);color:#C4B5FD;border-radius:20px;">
                Rendez-vous n°{{ $appointment->id }}
            </span>
            <h1 class="section-title-3d">Votre demande a bien été envoyée</h1>
            <p class="text-white-50 mb-4">
                Votre récitation vocale et votre demande de test Coran sont maintenant disponibles pour l’administration.
            </p>

            <div class="text-start mb-4" style="padding:18px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.08);border-radius:16px;">
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom" style="border-color:rgba(255,255,255,.06)!important;">
                    <span class="text-white-50">Matière</span><strong class="text-white">{{ $vocalSubmission->subject->name }}</strong>
                </div>
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom" style="border-color:rgba(255,255,255,.06)!important;">
                    <span class="text-white-50">Niveau</span><strong class="text-white">{{ $vocalSubmission->level->name }}</strong>
                </div>
                <div class="d-flex justify-content-between gap-3 py-2 border-bottom" style="border-color:rgba(255,255,255,.06)!important;">
                    <span class="text-white-50">Classe</span><strong class="text-white">{{ $vocalSubmission->classRoom->name }}</strong>
                </div>
                <div class="d-flex justify-content-between gap-3 py-2">
                    <span class="text-white-50">Statut</span><strong style="color:#FCD34D;">En attente</strong>
                </div>
            </div>

            <p class="text-white-50 small">L’administration vous contactera pour confirmer la date du test.</p>
            <a href="{{ route('home') }}" class="btn-3d btn-3d-gradient mt-3">
                <i class="bi bi-house me-2"></i> Retour à l’accueil
            </a>
        </div>
    </div>
</section>
@endsection
