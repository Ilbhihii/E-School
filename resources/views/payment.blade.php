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
                            <p>Après paiement, envoyez reçu à l\'admin.</p>
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
                    <div class="card-header bg-dark text-white text-center py-4">
                        <h4>Paiement par carte sécurisée</h4>
                    </div>
                    <div class="card-body p-4">

                        <!-- Logos cartes -->
                        <div class="text-center mb-4">
                            <img width="50" src="https://img.icons8.com/color/48/visa.png"/>
                            <img width="50" src="https://img.icons8.com/color/48/mastercard.png"/>
                            <img width="50" src="https://img.icons8.com/color/48/paypal.png"/>
                        </div>

                        <form id="payment-form">

                            <div class="mb-3">
                                <label>Nom du titulaire</label>
                                <input type="text" class="form-control" id="card-name" required>
                            </div>

                            <div class="mb-3">
                                <label>Informations carte</label>
                                <div id="card-element" class="form-control p-3"></div>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">
                                Payer 99 MAD maintenant
                            </button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Stripe Script (only for card payment) -->
@if(!request('method') || request('method') != 'paypal' && request('method') != 'bank')
<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create("card", {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#a0aec0'
                }
            }
        }
    });
    card.mount("#card-element");

    const form = document.getElementById("payment-form");
    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: "card",
            card: card,
        });

        if(error){
            alert(error.message);
        }else{
            fetch("/process-payment",{
                method:"POST",
                headers:{
                    "Content-Type":"application/json",
                    "X-CSRF-TOKEN":"{{ csrf_token() }}"
                },
                body:JSON.stringify({
                    payment_method:paymentMethod.id
                })
            }).then(res=>res.json())
            .then(data=>{
                alert("Paiement réussi !");
                window.location.href = "{{ route('student.dashboard') }}";
            }).catch(err => alert("Erreur: " + err));
        }
    });
</script>

<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=USD"></script>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '10.00'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            alert('Paiement réussi par PayPal');
            window.location.href = "/payment-success";
        });
    }
}).render('#paypal-button-container');
</script>

@endif

@endsection

