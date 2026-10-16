<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .company { font-size: 24px; font-weight: bold; color: #2d6a4f; }
        .invoice-title { font-size: 20px; margin-top: 20px; }
        .info { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">Easy Point Admin</div>
        <div class="invoice-title">FACTURE</div>
        <p>N° {{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p>Date : {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <strong>Client :</strong> {{ $subscription->company->compagny_name }}<br>
        <strong>Email :</strong> {{ $subscription->company->compagny_email }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Abonnement {{ $subscription->plan->name }} ({{ $subscription->duration_months }} mois)</td>
                <td>{{ $subscription->duration_months }}</td>
                <td>{{ number_format($subscription->plan->price, 0, ',', ' ') }} F CFA</td>
                <td>{{ number_format($subscription->total_amount, 0, ',', ' ') }} F CFA</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Total : {{ number_format($subscription->total_amount, 0, ',', ' ') }} F CFA
    </div>

    <div class="footer">
        <p>Merci de votre confiance !</p>
        <p>Easy Point Admin - Votre solution de gestion</p>
    </div>
</body>
</html>
