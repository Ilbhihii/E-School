@extends('layouts.front')

@section('content')

<div class="container mt-5 py-5">

    <h2 class="text-center mb-4">Paiement abonnement</h2>

    @if(request('method') === 'paypal')

    <div class="max-w-md mx-auto bg-white shadow-xl rounded-2xl p-6 text-center">

        <!-- QR Code -->
        <div class="bg-gray-100 rounded-2xl p-4 mb-4">
            <img src="{{ asset('images/paypal-qr.png') }}" 
                alt="QR PayPal"
                class="mx-auto rounded-xl w-64 h-64 object-contain">
        </div>

        <p class="text-gray-600 mb-4">
            Scannez ce code avec PayPal pour payer
        </p>


    </div>


        </div>
    </div>



    @elseif(request('method') == 'bank')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h4>Virement bancaire (Banque Populaire)</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <p><strong>Banque :</strong> Banque Populaire</p>
                            <p><strong>RIB :</strong> 123456789012345678901234</p>
                            <p><strong>Nom :</strong> Votre Société</p>
                            <p>Après paiement, envoyez le reçu à l'administrateur pour activer votre compte.</p>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('plans') }}" class="btn btn-outline-primary">Retour aux plans</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h4>Choisissez votre méthode de paiement</h4>
                    </div>
                    <div class="card-body p-5 text-center">
                        <p class="text-muted mb-4">Sélectionnez votre moyen de paiement préféré</p>
                        <div class="d-grid gap-3">
                            <a href="{{ route('student.payment') }}?method=paypal" class="btn btn-primary btn-lg rounded-pill">
                                🅿️ Payer avec PayPal
                            </a>
                            <a href="{{ route('student.payment') }}?method=bank" class="btn btn-outline-dark btn-lg rounded-pill">
                                🏦 Virement bancaire
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

@endsection

