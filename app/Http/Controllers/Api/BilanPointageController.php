<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BilanPointageController extends Controller
{
    //
public function bilanRotation(Request $request)
{
    $user = $request->user();

    if ($user->type_horaire !== 'rotation') {
        return response()->json(['message' => 'Utilisateur non concerné par les horaires de rotation.'], 403);
    }

    $today = now()->toDateString();
    $currentMonth = now()->format('Y-m');

    // Récupérer tous les pointages du mois courant
    $pointages = DB::table('pointages')
        ->where('user_id', $user->id)
        ->where('date_pointage', 'like', "$currentMonth%")
        ->get();

    // Statuts par date
    $calendar = [];
    $totalSeconds = 0;
    $retardCount = 0;
    $absenceCount = 0;

    foreach ($pointages->groupBy('date_pointage') as $date => $entries) {
        $statut = $entries->first()->statut;

        // Calcule du temps de travail si type = rotation (entre arrivée et départ)
        $arrivee = $entries->where('type', 'arrivee')->first();
        $depart = $entries->where('type', 'depart')->first();
        $duration = 0;

        if ($arrivee && $depart) {
            $start = strtotime($arrivee->heure_pointage);
            $end = strtotime($depart->heure_pointage);
            $duration = max(0, $end - $start);
            $totalSeconds += $duration;
        }

        if ($statut === 'retard') $retardCount++;
        if ($statut === 'absence') $absenceCount++;

        $calendar[$date] = [
            'statut' => $statut,
            'duration' => gmdate('H\h i\m s\s', $duration),
        ];
    }

    // Infos du jour
    $todayStatus = $calendar[$today] ?? [
        'statut' => 'absence',
        'duration' => '00h 00min 00s',
    ];

    $data = [
        'aujourdhui' => [
            'retard' => $todayStatus['statut'] === 'retard' ? 'Oui' : 'Non',
            'absence' => $todayStatus['statut'] === 'absence' ? 'Oui' : 'Non',
            'temps' => $todayStatus['duration'],
        ],
        'mois' => [
            'retard' => $retardCount,
            'absence' => $absenceCount,
            'temps' => gmdate('H\h i\m s\s', $totalSeconds),
        ],
        'calendrier' => $calendar,
    ];

    return response()->json($data);
}

}
