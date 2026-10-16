<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentArchiveService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ArchiveOldPayments extends Command
{
    protected $signature = 'payments:archive-old {months=6}';
    protected $description = 'Archive payments older than X months';

    protected $archiveService;

    public function __construct(PaymentArchiveService $archiveService)
    {
        parent::__construct();
        $this->archiveService = $archiveService;
    }

    public function handle()
    {
        $months = $this->argument('months');
        $date = Carbon::now()->subMonths($months);

        $this->info("Archiving payments older than {$months} months...");

        $oldPayments = Payment::where('created_at', '<', $date)
            ->whereDoesntHave('company') // Compagnies qui n'existent plus
            ->get();

        $bar = $this->output->createProgressBar(count($oldPayments));
        $bar->start();

        $count = 0;
        foreach ($oldPayments as $payment) {
            try {
                $this->archiveService->archivePayment($payment);
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to archive payment {$payment->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();

        $this->newLine();
        $this->info("Archived {$count} payments.");
    }
}
