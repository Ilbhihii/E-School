@extends('layouts.front')

@section('content')

<div class="container mt-5 py-5">

    <h2 class="text-center mb-4">Paiement abonnement</h2>

    @if(request('method') === 'paypal')

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4><i class="bi bi-paypal"></i> Paiement PayPal</h4>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-paypal" style="font-size: 4rem; color: #0070BA;"></i>
                    </div>
                    <p class="text-muted mb-4">
                        Cliquez sur le bouton ci-dessous pour être redirigé vers PayPal et effectuer votre paiement en toute sécurité.
                    </p>
                    <a href="https://www.paypal.me/abdelghanimaloulou1" target="_blank" class="btn btn-primary btn-lg rounded-pill px-5">
                        <i class="bi bi-paypal"></i> Payer avec PayPal
                    </a>
                    <p class="mt-3 small text-muted">
                        Une fois le paiement effectué, veuillez envoyer la confirmation à l'administrateur.
                    </p>
                    <div class="text-center mt-4">
                        <a href="{{ route('plans') }}" class="btn btn-outline-secondary rounded-pill">Retour aux plans</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(request('method') == 'bank')
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4 mb-4">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h4><i class="bi bi-bank"></i> Virement bancaire — Maroc</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <p><strong>Banque :</strong> Banque Populaire</p>
                            <p><strong>RIB :</strong> 123456789012345678901234</p>
                            <p><strong>Nom :</strong> M. Abdelghani Maloulou</p>
                        </div>
                        <p class="text-muted small">
                            <i class="bi bi-info-circle"></i> Après paiement, veuillez envoyer le reçu à l'administrateur pour activer votre accès.
                        </p>
                    </div>
                </div>

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-info text-white text-center py-4">
                        <h4><i class="bi bi-bank"></i> Virement bancaire — France</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <p><strong>Compte :</strong> EUROCOMPTE SÉRÉNITÉ</p>
                            <p><strong>Titulaire :</strong> M. Abdelghani Maloulou</p>
                            <hr>
                            <p><strong>RIB :</strong> 10278 08976 00021074401 03</p>
                            <p><strong>IBAN :</strong> FR76 1027 8089 7600 0210 7440 103</p>
                            <p><strong>BIC :</strong> CMCIFR2A</p>
                        </div>
                        <p class="text-muted small">
                            <i class="bi bi-info-circle"></i> Après paiement, veuillez envoyer le reçu à l'administrateur pour activer votre accès.
                        </p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('plans') }}" class="btn btn-outline-primary rounded-pill">Retour aux plans</a>
                </div>
            </div>
        </div>

    @endif

</div>

@endsection

