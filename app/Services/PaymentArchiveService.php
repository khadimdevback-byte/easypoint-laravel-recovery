<?php
namespace App\Services;

use App\Models\PaymentArchive;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentArchiveService
{
    /**
     * Archiver tous les paiements d'une compagnie
     */
    public function archiveCompanyPayments(Company $company): int
    {
        try {
            DB::beginTransaction();

            $count = 0;

            // Récupérer tous les paiements de la compagnie
            $payments = Payment::where('company_id', $company->id)->get();

            foreach ($payments as $payment) {
                $this->archivePayment($payment, $company);
                $count++;
            }

            // Archiver aussi les souscriptions sans paiements (si besoin)
            $this->archiveSubscriptionsWithoutPayments($company);

            DB::commit();

            Log::info('Company payments archived', [
                'company_id' => $company->id,
                'count' => $count
            ]);

            return $count;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to archive company payments', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Archiver un paiement individuel
     */
    public function archivePayment(Payment $payment, ?Company $company = null): PaymentArchive
    {
        $company = $company ?? $payment->company;
        $subscription = $payment->subscription;

        return PaymentArchive::create([
            // Données de la compagnie
            'company_id' => $company?->id,
            'company_name' => $company?->compagny_name ?? 'N/A',
            'company_email' => $company?->email ?? 'N/A',
            'company_phone' => $company?->phone ?? null,

            // Données de la souscription
            'subscription_id' => $subscription?->id,
            'plan_name' => $subscription?->plan?->name ?? 'N/A',
            'duration_months' => $subscription?->duration_months,

            // Données du paiement
            'fedapay_transaction_id' => $payment->fedapay_transaction_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'XOF',
            'payment_method' => $payment->payment_method,
            'status' => $payment->status,

            // Backup complet (au cas où)
            'full_payment_data' => $payment->toArray(),
            'full_company_data' => $company?->toArray(),
            'full_subscription_data' => $subscription?->load('plan')->toArray(),

            // Dates
            'payment_date' => $payment->created_at,
            'archived_at' => now(),
        ]);
    }

    /**
     * Archiver les souscriptions sans paiements associés
     */
    private function archiveSubscriptionsWithoutPayments(Company $company): void
    {
        $subscriptions = Subscription::where('company_id', $company->id)
            ->whereDoesntHave('payments')
            ->get();

        foreach ($subscriptions as $subscription) {
            PaymentArchive::create([
                'company_id' => $company->id,
                'company_name' => $company->compagny_name,
                'company_email' => $company->email,
                'subscription_id' => $subscription->id,
                'plan_name' => $subscription->plan?->name ?? 'N/A',
                'duration_months' => $subscription->duration_months,
                'amount' => $subscription->total_amount ?? 0,
                'status' => $subscription->status,
                'full_subscription_data' => $subscription->toArray(),
                'payment_date' => $subscription->created_at,
                'archived_at' => now(),
            ]);
        }
    }

    /**
     * Archiver automatiquement lors de la suppression d'une compagnie
     */
    public function handleCompanyDeletion(Company $company): void
    {
        $this->archiveCompanyPayments($company);
    }
}
