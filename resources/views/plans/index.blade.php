@extends('layouts.front')

@section('title', 'Plans Premium')

@section('content')

<div class="min-vh-100 d-flex align-items-center" style="background:#F5F7FA;">

    <div class="container py-5">

        <!-- HEADER -->
        <div class="text-center mb-5">
            <h1 class="fw-bold text-black">Choisissez votre abonnement</h1>
            <p class="text-muted">Accès illimité aux cours premium</p>
        </div>

        <div class="row justify-content-center">

            <!-- PLAN PREMIUM -->
            <div class="col-md-4">

                <div class="card plan-card border-0 shadow-sm text-center p-4">

                    <!-- Badge -->
                    <span class="badge bg-primary mb-3">
                        ⭐ Recommandé
                    </span>

                    <h3 class="fw-bold">Premium</h3>

                    <h2 class="fw-bold text-primary my-3">
                        200€
                    </h2>

                    <!-- Features -->
                    <ul class="list-unstyled text-muted mb-4">
                        <li>✔ Accès à tous les cours</li>
                        <li>✔ Lives illimités</li>
                        <li>✔ Téléchargement PDF</li>
                        <li>✔ Support prioritaire</li>
                    </ul>

                    <!-- Boutons -->
                    <div class="d-grid gap-2">

                        <a href="{{ route('student.payment') }}?method=paypal"
                           class="btn btn-primary rounded-pill">
                            🅿️ PayPal
                        </a>

                        <a href="{{ route('student.payment') }}?method=bank"
                           class="btn btn-outline-dark rounded-pill">
                            🏦 Virement bancaire
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- STYLE -->
<style>
.plan-card {
    border-radius: 20px;
    transition: all 0.3s ease;
}

.plan-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

/* boutons hover */
.plan-card .btn:hover {
    transform: scale(1.05);
    transition: 0.3s;
}
</style>

@endsection
