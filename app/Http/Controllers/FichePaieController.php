<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, Company, FichePaie, HeureMensuelle, Pointage, Site, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CotisationSociale;
use Illuminate\Support\Facades\Log;
class FichePaieController extends Controller
{
    public function formulaire()
    {
        return view('fichePaie.formulaire');
    }

    public function showFormGenererFixe()
    {
        return view('fichePaie.fichepaie_genererFixe');
    }

    public function showFormGenererRotatif()
    {
        return view('fichePaie.fichepaie_genererRotatif');
    }

    public function genererFixe(Request $request)
    {
        return $this->genererFiches('fixe', $request);
    }

    public function genererRotatif(Request $request)
    {
        return $this->genererFiches('rotation', $request);
    }

    private function genererFiches(string $typeHoraire, Request $request)
    {
        $mois = $request->input('mois', now()->format('m'));
        $annee = $request->input('annee', now()->year);

        if ($annee < now()->year) {
            return back()->with('error', 'Vous ne pouvez pas générer de fiche pour une année passée.');
        }

        $company = Auth::user()->company;
        $salaireFixeForm = $typeHoraire === 'fixe' ? $request->input('salaire_fixe') : null;

        $users = User::where('company_id', $company->id)
            ->where('user_type', 'user')
            ->where('type_horaire', $typeHoraire)
            ->get();

        if ($users->isEmpty()) {
            return back()->with('error', "Aucun employé avec le type d'horaire '{$typeHoraire}' trouvé.");
        }

        foreach ($users as $user) {
            $estExceptionFicheFixe = $user->exception_fiche_fixe ?? false;

            $heuresTravaillees = 0;
            $tauxHoraire = 0;
            $salaireFixe = null;
            $salaireBrut = 0;
            $cotisationsDetails = [];

            // if ($typeHoraire === 'fixe' || ($typeHoraire === 'rotation' && $estExceptionFicheFixe)) {
            //     // $salaireFixe = $salaireFixeForm ?? $user->salaire_fixe;
            //     if (empty($salaireFixe)) {
            //         return back()->with('error', "L'employé {$user->name} n'a pas de salaire fixe défini !");
            //     }
            //     $salaireBrut = $salaireFixe;
            // } else {
                if (empty($user->salary_rate)) {
                    return back()->with('error', "L'employé {$user->name} n'a pas de taux horaire défini !");
                

                $attendances = Attendance::where('user_id', $user->id)
                    ->whereYear('timestamp', $annee)
                    ->whereMonth('timestamp', $mois)
                    ->orderBy('timestamp')
                    ->get();

                $workedMinutes = 0;
                $lastArrivalTime = null;

                foreach ($attendances as $attendance) {
                    $timestamp = Carbon::parse($attendance->timestamp);
                    $dayOfWeek = strtolower($timestamp->format('l'));

                    $startTime = $user->{'start_time_' . $dayOfWeek} ?? null;
                    $startTime = $startTime ? Carbon::parse($startTime) : null;

                    if (!$user->{$dayOfWeek . '_checked'} && $attendance->site) {
                        $siteStart = $attendance->site->{$dayOfWeek . '_start_time'} ?? null;
                        $startTime = $siteStart ? Carbon::parse($siteStart) : $startTime;
                    }

                    if ($attendance->type === 'arrivee') {
                        $lastArrivalTime = $timestamp;
                    } elseif ($attendance->type === 'depart' && $lastArrivalTime) {
                        $workedMinutes += $lastArrivalTime->diffInMinutes($timestamp);
                        $lastArrivalTime = null;
                    }
                }

                $heuresTravaillees = round($workedMinutes / 60, 2);
                $tauxHoraire = $user->salary_rate;
                $salaireBrut = round($heuresTravaillees * $tauxHoraire, 2);
            }

            // Cotisations
            $cotisationsDetails = $company->cotisations->map(function ($cotisation) use ($salaireBrut) {
                return [
                    'libelle' => $cotisation->libelle,
                    'base' => $salaireBrut,
                    'taux' => $cotisation->taux_salarial,
                    'part_salariale' => round($salaireBrut * ($cotisation->taux_salarial / 100), 2),
                    'part_employeur' => round($salaireBrut * ($cotisation->taux_employeur / 100), 2),
                ];
            })->toArray();

            $cotisations = array_sum(array_column($cotisationsDetails, 'part_salariale'));

            // Autres éléments (personnalisables plus tard)
            $primes = 0;
            $retenues = 0;
            $avancements = 0;

            // Calcul net
            $netSocial = $salaireBrut + $primes - $retenues - $cotisations - $avancements;
            $netAvantImpot = $netSocial;
            $impotSource = 0;
            $salaireNet = $netAvantImpot - $impotSource;

            $periodeDebut = Carbon::create($annee, $mois, 1)->startOfMonth();
            $periodeFin = Carbon::create($annee, $mois, 1)->endOfMonth();

            $existe = FichePaie::where('user_id', $user->id)
                ->where('mois', $mois)
                ->where('annee', $annee)
                ->first();

            if (!$existe) {
                FichePaie::create([
                    'user_id' => $user->id,
                    'mois' => $mois,
                    'annee' => $annee,
                    'periode_debut' => $periodeDebut,
                    'periode_fin' => $periodeFin,
                    'heures_travaillees' => $heuresTravaillees,
                    'taux_horaire' => $tauxHoraire,
                    'salaire_fixe' => $salaireFixe,
                    'salaire_brut' => $salaireBrut,
                    'primes' => $primes,
                    'retenues' => $retenues,
                    'cotisations' => $cotisations,
                    'avancements' => $avancements,
                    'cotisations_details' => json_encode($cotisationsDetails),
                    'net_social' => $netSocial,
                    'net_avant_impot' => $netAvantImpot,
                    'impot_source' => $impotSource,
                    'salaire_net' => $salaireNet,
                    'date_paiement' => null,
                    'horaire_type' => ($typeHoraire === 'rotation' && $estExceptionFicheFixe) ? 'fixe' : $typeHoraire,
                    'company_id' => $company->id,
                    'exception_fiche_fixe' => $estExceptionFicheFixe,
                ]);
            }
        }

        return back()->with('success', 'Fiches de paie générées avec succès.');
    }

    public function edit($ficheId)
    {
        $fichePaie = FichePaie::with('user')
            ->where('id', $ficheId)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        return view('fichePaie.edit', compact('fichePaie'));
    }

    public function update(Request $request, $id)
    {
        $fichePaie = FichePaie::findOrFail($id);
        $company = $fichePaie->company;

        $validated = $request->validate([
            'primes'        => 'nullable|numeric',
            'retenues'      => 'nullable|numeric',
            'avancements'   => 'nullable|numeric',
            'salaire_brut'  => 'required|numeric',
            'impot_source'  => 'nullable|numeric',
            'date_paiement' => 'nullable|date',
        ]);

        $salaireBrut = $validated['salaire_brut'];
        $primes = $validated['primes'] ?? 0;
        $retenues = $validated['retenues'] ?? 0;
        $avancements = $validated['avancements'] ?? 0;
        $impotSource = $validated['impot_source'] ?? 0;

        $cotisationsDetails = $company->cotisations->map(function ($cotisation) use ($salaireBrut) {
            return [
                'libelle' => $cotisation->libelle,
                'base' => $salaireBrut,
                'taux' => $cotisation->taux_salarial,
                'part_salariale' => round($salaireBrut * ($cotisation->taux_salarial / 100), 2),
                'part_employeur' => round($salaireBrut * ($cotisation->taux_employeur / 100), 2),
            ];
        })->toArray();

        $cotisations = array_sum(array_column($cotisationsDetails, 'part_salariale'));

        $netSocial = $salaireBrut + $primes - $retenues - $cotisations - $avancements;
        $netAvantImpot = $netSocial;
        $salaireNet = $netAvantImpot - $impotSource;

        $fichePaie->update([
            'salaire_brut' => $salaireBrut,
            'primes' => $primes,
            'retenues' => $retenues,
            'avancements' => $avancements,
            'cotisations' => $cotisations,
            'cotisations_details' => json_encode($cotisationsDetails),
            'net_social' => $netSocial,
            'net_avant_impot' => $netAvantImpot,
            'impot_source' => $impotSource,
            'salaire_net' => $salaireNet,
            'date_paiement' => $validated['date_paiement'] ?? null,
        ]);

        $route = $fichePaie->horaire_type === 'rotation' ? 'fichepaie.rotatif' : 'fichepaie.fixe';
        return redirect()->route($route)->with('success', 'Fiche de paie mise à jour avec succès.');
    }

    public function indexFixe(Request $request)
    {
        return $this->indexByHoraireType($request, 'fixe');
    }

    public function indexRotatif(Request $request)
    {
        return $this->indexByHoraireType($request, 'rotation');
    }

    private function indexByHoraireType(Request $request, $typeHoraire)
    {
        $companyId = Auth::user()->company_id;

        $fichePaies = FichePaie::with('user')
            ->where('company_id', $companyId)
            ->whereHas('user', fn($q) => $q->where('type_horaire', $typeHoraire))
            ->when($request->site_id, function ($q) use ($request) {
                $q->whereHas('user.sites', fn($sub) => $sub->where('sites.id', $request->site_id));
            })
            ->when($request->mois, fn($q) => $q->where('mois', $request->mois))
            ->when($request->annee, fn($q) => $q->where('annee', $request->annee))
            ->orderByDesc('annee')
            ->orderByDesc('mois')
            ->paginate(10);

        $sites = Site::where('company_id', $companyId)->get();
        $anneesDisponibles = FichePaie::where('company_id', $companyId)
            ->select('annee')
            ->distinct()
            ->orderByDesc('annee')
            ->pluck('annee')
            ->toArray();

        return view('fichePaie.index', compact('fichePaies', 'sites', 'anneesDisponibles', 'typeHoraire'));
    }

// public function show($id)
// {
//     $fiche = FichePaie::with(['user', 'company'])
//         ->where('company_id', Auth::user()->company_id)
//         ->findOrFail($id);

//     $brut = $fiche->salaire_brut + $fiche->primes;

//     // Récupérer les cotisations sociales liées à la société
//     $cotisationsModel = CotisationSociale::where('company_id', $fiche->company_id)->get();

//     $totalSalariale = 0;
//     $totalEmployeur = 0;

//     $cotisations = $cotisationsModel->map(function ($cotisation) use ($brut, &$totalSalariale, &$totalEmployeur) {
//         $partSalariale = round($brut * $cotisation->taux_salarial / 100, 2);
//         $partEmployeur = $cotisation->taux_employeur
//             ? round($brut * $cotisation->taux_employeur / 100, 2)
//             : 0.00;

//         $totalSalariale += $partSalariale;
//         $totalEmployeur += $partEmployeur;

//         return [
//             'libelle' => $cotisation->libelle,
//             'base' => $brut,
//             'taux' => $cotisation->taux_salarial,
//             'part_salariale' => $partSalariale,
//             'part_employeur' => $partEmployeur,
//         ];
//     })->toArray();

//     // Ajout des retenues et avancements dans le calcul
//     $retenues = $fiche->retenues ?? 0;
//     $avancements = $fiche->avancements ?? 0;

//     $netAvantImpot = $brut - $totalSalariale - $retenues - $avancements;
//     $netAPayer = $netAvantImpot - ($fiche->impot_source ?? 0);

//     // Fallback pour éviter erreur si company ou devise manquante
//     if (!$fiche->company || !isset($fiche->company->currency)) {
//         $fiche->company = (object)['currency' => '€'];
//     }

//     return view('fichePaie.show', compact(
//         'fiche',
//         'cotisations',
//         'brut',
//         'totalSalariale',
//         'totalEmployeur',
//         'netAvantImpot',
//         'netAPayer'
//     ));
// }




public function show($id)
{
    $fiche = FichePaie::with(['user', 'company'])
        ->where('company_id', Auth::user()->company_id)
        ->findOrFail($id);

    $user = $fiche->user;
    $mois = Carbon::parse($fiche->date_debut_periode)->month;
    $annee = Carbon::parse($fiche->date_debut_periode)->year;
    $salaryRate = $user->salary_rate;
    $typeHoraire = $user->type_horaire;

    $total_heures = 0;
    $montantHoraire = 0;
    $brut = 0;

    // 🔄 Récupérer les heures selon type_horaire
    $heureMensuelle = HeureMensuelle::where('user_id', $user->id)
        ->where('mois', $mois)
        ->where('annee', $annee)
        ->first();

    $total_heures = $heureMensuelle?->total_heures ?? 0;

    if ($typeHoraire === 'rotation' || $typeHoraire === 'fixe') {
        // 💰 Salaire calculé à partir du taux horaire
        if ($salaryRate) {
            $montantHoraire = round($total_heures * $salaryRate, 2);
        }

        $brut = $montantHoraire + $fiche->primes;
    }

    // 📊 Cotisations sociales
    $cotisationsModel = CotisationSociale::where('company_id', $fiche->company_id)->get();
    $totalSalariale = 0;
    $totalEmployeur = 0;

    $cotisations = $cotisationsModel->map(function ($cotisation) use ($brut, &$totalSalariale, &$totalEmployeur) {
        $partSalariale = round($brut * $cotisation->taux_salarial / 100, 2);
        $partEmployeur = $cotisation->taux_employeur
            ? round($brut * $cotisation->taux_employeur / 100, 2)
            : 0.00;

        $totalSalariale += $partSalariale;
        $totalEmployeur += $partEmployeur;

        return [
            'libelle' => $cotisation->libelle,
            'base' => $brut,
            'taux' => $cotisation->taux_salarial,
            'part_salariale' => $partSalariale,
            'part_employeur' => $partEmployeur,
        ];
    })->toArray();

    // Déductions
    $retenues = $fiche->retenues ?? 0;
    $avancements = $fiche->avancements ?? 0;
    $impotSource = $fiche->impot_source ?? 0;

    $netAvantImpot = $brut - $totalSalariale - $retenues - $avancements;
    $netAPayer = $netAvantImpot - $impotSource;

    // Devise par défaut
    if (!$fiche->company || !isset($fiche->company->currency)) {
        $fiche->company = (object)['currency' => '€'];
    }

    return view('fichePaie.show', compact(
        'fiche',
        'cotisations',
        'brut',
        'totalSalariale',
        'totalEmployeur',
        'netAvantImpot',
        'netAPayer',
        'total_heures',
        'montantHoraire'
    ));
}



// public function show($id)
// {
//     $fiche = FichePaie::with(['user', 'company'])
//         ->where('company_id', Auth::user()->company_id)
//         ->findOrFail($id);

//     $salaryRate = $fiche->user->salary_rate;
//     $mois = Carbon::parse($fiche->date_debut_periode)->month;
//     $annee = Carbon::parse($fiche->date_debut_periode)->year;

//     $total_heures = 0;
//     $montantRotation = 0;

//     // Pour les horaires à rotation
//     if ($fiche->user->type_horaire === 'rotation') {
//         $heureMensuelle = HeureMensuelle::where('user_id', $fiche->user_id)
//             ->where('mois', $mois)
//             ->where('annee', $annee)
//             ->first();

//         $total_heures = $heureMensuelle ? $heureMensuelle->total_heures : 0;
//         $montantRotation = round($total_heures * $salaryRate, 2);
//         $brut = $montantRotation + $fiche->primes;
//     } else {
//         // Pour les horaires fixes
//         $brut = $fiche->salaire_brut + $fiche->primes;
//     }

//     // Cotisations
//     $cotisationsModel = CotisationSociale::where('company_id', $fiche->company_id)->get();
//     $totalSalariale = 0;
//     $totalEmployeur = 0;

//     $cotisations = $cotisationsModel->map(function ($cotisation) use ($brut, &$totalSalariale, &$totalEmployeur) {
//         $partSalariale = round($brut * $cotisation->taux_salarial / 100, 2);
//         $partEmployeur = $cotisation->taux_employeur
//             ? round($brut * $cotisation->taux_employeur / 100, 2)
//             : 0.00;

//         $totalSalariale += $partSalariale;
//         $totalEmployeur += $partEmployeur;

//         return [
//             'libelle' => $cotisation->libelle,
//             'base' => $brut,
//             'taux' => $cotisation->taux_salarial,
//             'part_salariale' => $partSalariale,
//             'part_employeur' => $partEmployeur,
//         ];
//     })->toArray();

//     $retenues = $fiche->retenues ?? 0;
//     $avancements = $fiche->avancements ?? 0;
//     $netAvantImpot = $brut - $totalSalariale - $retenues - $avancements;
//     $netAPayer = $netAvantImpot - ($fiche->impot_source ?? 0);

//     if (!$fiche->company || !isset($fiche->company->currency)) {
//         $fiche->company = (object)['currency' => '€'];
//     }

//     return view('fichePaie.show', compact(
//         'fiche',
//         'cotisations',
//         'brut',
//         'totalSalariale',
//         'totalEmployeur',
//         'netAvantImpot',
//         'netAPayer',
//         'total_heures',
//         'montantRotation'
//     ));
// }


    public function delete($id)
    {
        $fiche = FichePaie::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $fiche->delete();

        return back()->with('success', 'Fiche de paie supprimée avec succès.');
    }
}
