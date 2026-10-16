<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offre;
use App\Models\Site;
use App\Models\SiteRotation;

class CorrigerOffresSites extends Command
{
    protected $signature = 'offres:corriger-sites';
    protected $description = 'Corrige les offres avec site_id ou site_rotation_id null selon le type_horaire';

    public function handle()
    {
        $this->info("📋 Recherche des incohérences...");

        $fixeInvalide = Offre::where('type_horaire', 'fixe')->whereNull('site_id')->get();
        $rotationInvalide = Offre::where('type_horaire', 'rotation')->whereNull('site_rotation_id')->get();

        $this->info("Offres fixes sans site_id : " . $fixeInvalide->count());
        $this->info("Offres rotation sans site_rotation_id : " . $rotationInvalide->count());

        if ($fixeInvalide->isEmpty() && $rotationInvalide->isEmpty()) {
            $this->info("✅ Aucune incohérence détectée.");
            return;
        }

        // Récupérer un site et une rotation par défaut
        $siteDefaut = Site::first();
        $rotationDefaut = SiteRotation::first();

        if (!$siteDefaut || !$rotationDefaut) {
            $this->error("❌ Impossible de corriger : aucun site ou site_rotation disponible.");
            return;
        }

        if ($this->confirm("Souhaitez-vous corriger automatiquement les erreurs ?")) {
            foreach ($fixeInvalide as $offre) {
                $offre->site_id = $siteDefaut->id;
                $offre->save();
                $this->info("✅ Offre #{$offre->id} corrigée (site_id => {$siteDefaut->id})");
            }

            foreach ($rotationInvalide as $offre) {
                $offre->site_rotation_id = $rotationDefaut->id;
                $offre->save();
                $this->info("✅ Offre #{$offre->id} corrigée (site_rotation_id => {$rotationDefaut->id})");
            }

            $this->info("🎉 Correction terminée.");
        } else {
            $this->info("❌ Correction annulée.");
        }
    }
}
