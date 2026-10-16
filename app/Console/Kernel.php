<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckAndMarkAbsences; // Importer votre commande

class Kernel extends ConsoleKernel
{
    /**
     * Définit le programme des commandes de l'application.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Exécuter la vérification des absences chaque nuit à une heure où tous les quarts de travail de la veille devraient être terminés.
        // Par exemple, 1h du matin (01:00) est généralement sûr.
        $schedule->command(CheckAndMarkAbsences::class)->dailyAt('01:00');

        // Archiver les paiements de plus de 6 mois chaque semaine
        $schedule->command('payments:archive-old 6')->weekly();

        // Nettoyer les archives de plus de 24 mois chaque mois
        $schedule->command('payments:clean-archives 24')->monthly();
    }

    /**
     * Enregistre les commandes pour l'application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        \App\Console\Commands\RecalculHeuresMensuelles::class,
        \App\Console\Commands\ServeCommand::class,
        \App\Console\Commands\ArchiveOldPayments::class,
        \App\Console\Commands\CleanPaymentArchives::class,
    ];

}
