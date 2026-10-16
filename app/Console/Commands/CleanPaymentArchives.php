<?php

namespace App\Console\Commands;

use App\Models\PaymentArchive;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanPaymentArchives extends Command
{
    protected $signature = 'payments:clean-archives {months=12}';
    protected $description = 'Delete payment archives older than X months';

    public function handle()
    {
        $months = $this->argument('months');
        $date = Carbon::now()->subMonths($months);

        $count = PaymentArchive::where('archived_at', '<', $date)->delete();

        $this->info("Deleted {$count} payment archives older than {$months} months.");
    }
}
