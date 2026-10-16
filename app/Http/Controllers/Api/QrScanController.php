<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use App\Models\{SiteRotation, Pointage, Equipe, Repartition};
use App\Services\RotationService;
use Carbon\Carbon;
use App\Exceptions\PointageForbiddenException; // Assurez-vous que ce namespace est correct et que le fichier existe

class QrScanController extends Controller
{
    protected RotationService $rotationService;
    protected int $margePointageMinutes = 0; // Marge pour un pointage anticipé ou tardif toléré

    public function __construct(RotationService $rotationService)
    {
        $this->rotationService = $rotationService;
    }

    public function scan(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateRequest($request);
            $user = $this->getAuthenticatedUser();
            $site = $this->verifySite($validated['qr_code'], $user->company_id);
            $this->verifyLocation($site, $validated['latitude'], $validated['longitude']);
            $equipe = $this->getUserTeam($user, $site);

            // Appel de la méthode pour obtenir les données de pointage,
            // qui inclura la logique de vérification des jours de repos.
            $pointageData = $this->preparePointageData($user, $site, $equipe, $validated);

            $pointage = Pointage::create($pointageData);
            // ✅ Calcul automatique des heures mensuelles pour le mois courant
            if ($pointage->type === 'depart') {
                app(\App\Services\CalculHeuresMensuellesService::class)
                    ->calculerPourUtilisateur($user, now()->month, now()->year);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'pointage' => $pointage,
                'message' => $this->getSuccessMessage($pointage->type)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleError($e);
        }
    }

    private function handleError(\Exception $e)
    {
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json(['error' => $e->errors()], 422);
        }

        // Gérer les exceptions spécifiques de blocage de pointage
        if ($e instanceof \App\Exceptions\PointageForbiddenException) {
            return response()->json(['error' => $e->getMessage()], 403); // 403 Forbidden
        }

        Log::error('Erreur pointage: '.$e->getMessage(), ['exception' => $e]);
        $code = method_exists($e, 'getCode') ? $e->getCode() : 500;
        $code = in_array($code, [401, 403, 404, 400]) ? $code : 500;

        return response()->json(['error' => $e->getMessage()], $code);
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'qr_code' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
    }

    protected function getAuthenticatedUser()
    {
        $user = Auth::user()->loadMissing(['equipes.rotations.repartitions', 'equipes.sites']);

        if (!$user?->company_id) {
            throw new \RuntimeException('Accès non autorisé', 401);
        }

        return $user;
    }

    protected function verifySite(string $qrCode, int $companyId): SiteRotation
    {
        $site = SiteRotation::where('qr_code', $qrCode)
            ->where('company_id', $companyId)
            ->first();

        if (!$site) {
            throw new \RuntimeException('Site non trouvé ou non autorisé', 404);
        }

        if (!$site->latitude || !$site->longitude || !$site->radius) {
            throw new \RuntimeException('Configuration géographique incomplète', 400);
        }

        return $site;
    }

    protected function verifyLocation(SiteRotation $site, float $lat, float $lng): bool
    {
        $distance = $this->calculateHaversineDistance(
            $site->latitude,
            $site->longitude,
            $lat,
            $lng
        );

        if ($distance > $site->radius) {
            throw new \RuntimeException(sprintf(
                'Vous êtes à %.0fm du site (limite: %dm)',
                $distance, $site->radius
            ), 403);
        }

        return true;
    }

    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // en mètres
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function getUserTeam($user, SiteRotation $site): Equipe
    {
        $equipe = $user->equipes->first(function ($equipe) use ($site) {
            return $equipe->sites->contains($site->id);
        });

        if (!$equipe) {
            Log::error('Autorisation refusée', [
                'user_id' => $user->id,
                'site_id' => $site->id,
                'equipes_user' => $user->equipes->pluck('id')
            ]);
            throw new \RuntimeException('Non autorisé sur ce site', 403);
        }

        return $equipe;
    }

    protected function preparePointageData($user, $site, $equipe, array $validated): array
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $data = [
            'user_id' => $user->id,
            'equipe_id' => $equipe->id,
            'site_id' => $site->id,
            'company_id' => $user->company_id,
            'date_pointage' => $today,
            'heure_pointage' => $now,
            'coordinates' => DB::raw("ST_GeomFromText('POINT({$validated['longitude']} {$validated['latitude']})')"),
            'type' => 'arrivee', // Valeur par défaut, sera modifiée si nécessaire
            'statut' => 'present' // Valeur par défaut, sera modifiée si nécessaire
        ];
        // Pour les utilisateurs sans rotation, on ne fait pas de vérification horaire stricte
        if ($user->type_horaire !== 'rotation') {
            $lastPointage = Pointage::where('user_id', $user->id)
                ->whereDate('date_pointage', $today)
                ->latest('heure_pointage')
                ->first();
            if ($lastPointage && $lastPointage->type === 'arrivee') {
                $data['type'] = 'depart';
            } else {
                $data['type'] = 'arrivee';
            }
            return $data;
        }
        Log::debug('QrScanController - Appel getCurrentPeriod', ['user_id' => $user->id, 'equipe_id' => $equipe->id, 'date' => $today->toDateString()]);
        $infosRotation = $this->rotationService->getCurrentPeriod($equipe, $today);
        $rotation = $infosRotation['rotation'] ?? null;
        $periode = $infosRotation['periode'] ?? null;
        $isWorkingDay = $infosRotation['is_working_day'] ?? false;
        $data['rotation_id'] = $rotation?->id;
        $data['periode_id'] = $periode?->id;
        Log::debug('QrScanController - Infos Rotation reçues', [
            'isWorkingDay' => $isWorkingDay,
            'periode_id' => $periode?->id,
            'periode_nom' => $periode?->periode,
            'rotation_id' => $rotation?->id
        ]);
        // 1. Gérer les jours non travaillés (jours de repos) : BLOQUER LE POINTAGE
        if (!$isWorkingDay) {
            Log::info('Pointage Forbidden: Tentative de pointage un jour de repos.', [
                'user_id' => $user->id,
                'date' => $today->toDateString(),
                'equipe_id' => $equipe->id,
                'rotation_id' => $rotation?->id
            ]);
            throw new PointageForbiddenException('Vous ne pouvez pas pointer un jour de repos.');
        }
        // 2. Gérer les jours travaillés sans période configurée (erreur de configuration) : BLOQUER LE POINTAGE
        if (!$periode) {
            Log::error('Pointage Forbidden: Période manquante pour un jour de travail.', ['user_id' => $user->id, 'date' => $today->toDateString(), 'equipe_id' => $equipe->id, 'isWorkingDay' => $isWorkingDay]);
            throw new PointageForbiddenException('Impossible de pointer : configuration de la rotation incomplète pour aujourd\'hui.');
        }
        // 3. Logique pour un jour de travail normal avec une période définie
        $heureDebutTheorique = Carbon::parse($today->format('Y-m-d') . ' ' . $periode->heure_debut);
        $heureFinTheorique = Carbon::parse($today->format('Y-m-d') . ' ' . $periode->heure_fin);
        // Gérer les horaires qui chevauchent minuit
        if ($heureFinTheorique->lte($heureDebutTheorique)) {
            $heureFinTheorique->addDay();
        }
        // Déterminer le type de pointage (arrivée/départ)
        $lastPointage = Pointage::where('user_id', $user->id)
            ->where('date_pointage', $today)
            ->whereIn('statut', ['present', 'retard']) // Ne considérer que les pointages réels
            ->latest('heure_pointage')
            ->first();
        // *** DEBUT DE LA LOGIQUE MODIFIÉE POUR GÉRER LE POINTAGE APRÈS FIN DE PÉRIODE ***

        // Si le dernier pointage est une ARRIVÉE, on s'attend à un DÉPART.
        if ($lastPointage && $lastPointage->type === 'arrivee') {
            $data['type'] = 'depart';
            // Pour un départ, on se base sur l'heure de fin théorique de la période en cours.
            // On permet un départ même si on est après l'heure de fin, jusqu'à une certaine tolérance.
            $toleranceFinPointageDepartMinutes = 120; // Exemple : Tolérer un départ jusqu'à 2 heures (120 min) après la fin théorique.
                                                    // Cela couvre les heures de nuit qui dépassent minuit.
            $heureMaxPourDepart = $heureFinTheorique->copy()->addMinutes($toleranceFinPointageDepartMinutes);
            // Si le pointage est TRÈS longtemps après l'heure de fin théorique (au-delà de la tolérance de départ)
            if ($now->greaterThan($heureMaxPourDepart)) {
                Log::info('Pointage Forbidden: Tentative de pointage de départ après l\'heure maximale tolérée post-période.', [
                    'user_id' => $user->id,
                    'current_time' => $now->toDateTimeString(),
                    'heure_fin_theorique' => $heureFinTheorique->toDateTimeString(),
                    'heure_max_pour_depart' => $heureMaxPourDepart->toDateTimeString()
                ]);
                throw new PointageForbiddenException('Impossible de pointer le départ : la période de travail est terminée depuis trop longtemps.');
            }
            // Si le pointage est trop tôt avant la fin théorique de la période
            // (La marge de pointage s'applique ici pour empêcher un départ trop anticipé)
            $limitePointageDepartAnticipe = $heureFinTheorique->copy()->subMinutes($this->margePointageMinutes);
            if ($now->lt($limitePointageDepartAnticipe)) {
                $message = 'Impossible de pointer le départ : la période de travail n\'est pas encore terminée. Vous pouvez pointer le départ à partir de ' . $limitePointageDepartAnticipe->format('H:i') . '.';
                throw new PointageForbiddenException($message);
            }
            $data['statut'] = 'present'; // Le statut reste présent, même si en dépassement.

        } else { // type === 'arrivee' (ou aucun pointage précédent aujourd'hui)
            $data['type'] = 'arrivee';
            // Pour une ARRIVÉE, on est strict : l'arrivée doit être pendant ou juste avant/après le début de la période
            // et IMPÉRATIVEMENT pas après la fin de la période (avec une très petite tolérance si besoin).
            $toleranceFinPointageArriveeMinutes = 5; // Une petite tolérance si l'arrivée est juste après la fin théorique.
            $heureMaxPourArrivee = $heureFinTheorique->copy()->addMinutes($toleranceFinPointageArriveeMinutes);

            // Bloquer l'arrivée si elle est APRÈS l'heure de fin théorique de la période (plus une petite tolérance)
            if ($now->greaterThan($heureMaxPourArrivee)) {
                Log::info('Pointage Forbidden: Tentative de pointage d\'arrivée après l\'heure maximale autorisée pour cette période.', [
                    'user_id' => $user->id,
                    'current_time' => $now->toDateTimeString(),
                    'heure_fin_theorique' => $heureFinTheorique->toDateTimeString(),
                    'heure_max_pour_arrivee' => $heureMaxPourArrivee->toDateTimeString()
                ]);
                throw new PointageForbiddenException('Impossible de pointer l\'arrivée : la période de travail pour aujourd\'hui est déjà terminée.');
            }
            // L'arrivée ne peut pas être avant l'heure de début théorique (avec marge 0 comme défini).
            $limitePointageArriveeAnticipe = $heureDebutTheorique->copy()->subMinutes(0);
            if ($now->lt($limitePointageArriveeAnticipe)) {
                $message = 'Impossible de pointer l\'arrivée : la période de travail n\'a pas encore débuté. Vous pouvez pointer l\'arrivée à partir de ' . $limitePointageArriveeAnticipe->format('H:i') . '.';
                throw new PointageForbiddenException($message);
            }

            // Si l'arrivée est après l'heure de début théorique plus la marge, c'est un retard
            if ($now->greaterThan($heureDebutTheorique->copy()->addMinutes($this->margePointageMinutes))) {
                $data['statut'] = 'retard';
            } else {
                $data['statut'] = 'present';
            }
        }

        // *** FIN DE LA LOGIQUE MODIFIÉE ***

        Log::info('Pointage autorisé.', [
            'user_id' => $user->id,
            'type' => $data['type'],
            'statut' => $data['statut'],
            'heure_pointage' => $data['heure_pointage']->toDateTimeString(),
            'heure_debut_theorique' => $heureDebutTheorique->toDateTimeString(),
            'heure_fin_theorique' => $heureFinTheorique->toDateTimeString()
        ]);
        return $data;
    }

    protected function getSuccessMessage(string $type): string
    {
        if ($type === 'arrivee') {
            return 'Pointage d\'arrivée enregistré avec succès.';
        } elseif ($type === 'depart') {
            return 'Pointage de départ enregistré avec succès.';
        }
        return 'Pointage enregistré avec succès.';
    }
}
