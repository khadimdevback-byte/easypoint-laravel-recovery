<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser votre inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        .payment-card { border: none; border-radius: 12px; }
        .btn-fedapay {
            background-color: #002855; /* Couleur bleu profond corporate type FedaPay */
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            transition: background 0.3s ease;
        }
        .btn-fedapay:hover {
            background-color: #001f42;
            color: white;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
            transition: background 0.3s ease;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-cancel i, .btn-fedapay i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-primary">Finalisez votre inscription</h2>
        <p class="lead text-muted">Merci d'avoir enregistré <strong>{{ $company->compagny_name }}</strong>.</p>
        <p>Pour activer votre abonnement au plan <span class="badge bg-primary fs-6">{{ $subscription->plan->name }}</span>, veuillez procéder au règlement.</p>
    </div>

    {{-- Formulaire sécurisé englobant les données de commande --}}
    <form id="payment_form" action="{{ route('registration.payment.initiate', ['company' => $company->id]) }}" method="POST">
        @csrf

        <div class="card payment-card shadow mx-auto p-4" style="max-width: 500px;">
            <h4 class="mb-3 fw-semibold">Détails de la commande</h4>
            <hr>

            <!-- Prix de base caché pour le calcul JS uniquement -->
            <input type="hidden" id="base_price" value="{{ $subscription->plan->price }}">

            <div class="mb-4">
                <label for="months_count" class="form-label fw-bold text-secondary">Durée de l'abonnement :</label>
                <div class="input-group" style="max-width: 200px;">
                    <input type="number" id="months_count" name="months" class="form-control text-center fw-bold" min="1" max="36" value="{{ $subscription->duration_months ?? 1 }}">
                    <span class="input-group-text fw-semibold">Mois</span>
                </div>
                <small class="text-muted">* Ajustez le nombre de mois selon vos besoins.</small>
            </div>

            <div class="d-flex justify-content-between my-2 align-items-center bg-light p-3 rounded">
                <span class="fw-semibold text-secondary">Montant total :</span>
                <div>
                    <strong class="text-success fs-4" id="total_display">
                        {{ number_format($subscription->total_amount, 0, ',', ' ') }}
                    </strong>
                    <strong class="text-success fs-4"> F CFA</strong>
                </div>
            </div>

            <div class="mt-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <button type="submit" id="pay_now_btn" class="btn btn-fedapay shadow-sm">
                            <i class="fas fa-credit-card me-2"></i> Payer avec FedaPay
                        </button>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="{{ route('dashboard') }}" class="btn btn-cancel shadow-sm w-100 text-decoration-none d-inline-block text-center">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    </div>
                </div>
                <p class="text-muted text-center mt-3 mb-0 small">
                    <i class="fas fa-info-circle"></i> En annulant, vous pourrez finaliser votre inscription plus tard.
                </p>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthsInput = document.getElementById('months_count');
    const totalDisplay = document.getElementById('total_display');
    const paymentForm = document.getElementById('payment_form');
    const payNowBtn = document.getElementById('pay_now_btn');
    const basePrice = parseFloat(document.getElementById('base_price').value);

    let dynamicTotalAmount = basePrice * (parseInt(monthsInput.value) || 1);

    function updateCalculatedTotal() {
        let months = parseInt(monthsInput.value);

        if (isNaN(months) || months < 1) {
            months = 1;
        }

        dynamicTotalAmount = basePrice * months;
        totalDisplay.textContent = dynamicTotalAmount.toLocaleString('fr-FR');
    }

    // Écouteurs pour la mise à jour visuelle du montant
    monthsInput.addEventListener('input', updateCalculatedTotal);
    monthsInput.addEventListener('change', updateCalculatedTotal);

    monthsInput.addEventListener('blur', function() {
        if (!monthsInput.value || monthsInput.value < 1) {
            monthsInput.value = 1;
            updateCalculatedTotal();
        }
    });

    // Intercepter la soumission pour la boîte de dialogue et la sécurité anti-double-clic
    paymentForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Suspend la soumission directe

        if (confirm(`Êtes-vous sûr de vouloir payer ${dynamicTotalAmount.toLocaleString('fr-FR')} F CFA pour ${monthsInput.value} mois d'abonnement ?`)) {
            // Désactive le bouton pour empêcher le spam du serveur pendant le traitement
            payNowBtn.disabled = true;
            payNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Redirection...';

            // Exécute la soumission native du formulaire
            paymentForm.submit();
        }
    });
});
</script>
</body>
</html>
