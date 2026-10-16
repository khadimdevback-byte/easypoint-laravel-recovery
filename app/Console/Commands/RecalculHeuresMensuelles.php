<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\CalculHeuresMensuellesService;

class RecalculHeuresMensuelles extends Command
{
    protected $signature = 'heures:recalculer {--mois=} {--annee=}';

    protected $description = 'Recalcule les heures mensuelles pour tous les utilisateurs';

    protected $service;

    public function __construct(CalculHeuresMensuellesService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle()
    {
        $mois = $this->option('mois') ?? now()->month;
        $annee = $this->option('annee') ?? now()->year;

        $this->info("Recalcul des heures pour le mois $mois/$annee...");

        $users = User::where('type_horaire', 'rotation')->get();
        foreach ($users as $user) {
            $this->service->calculerPourUtilisateur($user, $mois, $annee);
            $this->line(" - {$user->name} traité.");
        }

        $this->info("✅ Recalcul terminé.");
    }
}
