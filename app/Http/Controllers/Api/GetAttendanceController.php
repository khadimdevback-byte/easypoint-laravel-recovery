<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GetAttendanceController extends Controller
{
    public function getMonthlyAttendanceData(Request $request)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', 
            'year' => 'nullable|integer|min:2000|max:' . now()->year,
            'month' => 'nullable|integer|min:1|max:12',
        ]);
    
        // Récupération des paramètres
        $userId = $validated['user_id'];
        $year = $validated['year'] ?? now()->year;
        $month = $validated['month'] ?? now()->month;
    
        // Récupérer les données de présence pour l'utilisateur spécifié
        $attendances = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month)
            ->orderBy('timestamp')
            ->get();
    
        $dailyData = [];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
    
        // Parcours de chaque jour du mois pour calculer les durées et statuts
        for ($day = 1; $day <= $daysInMonth; $day++) {
            if (Carbon::create($year, $month, $day)->isFuture()) {
                continue;
            }
    
            $dayAttendances = $attendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->day == $day);
    
            if ($dayAttendances->isEmpty()) {
                $dailyData[$day] = ['status' => 'absent', 'timestamp' => null];
            } else {
                $firstAttendance = $dayAttendances->first();
                $dailyData[$day] = [
                    'timestamp' => $firstAttendance->timestamp,
                    'status' => $firstAttendance->status,
                ];
            }
        }
    
        // Récupération des présences du mois en cours
        $currentMonthAttendances = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', now()->year)
            ->whereMonth('timestamp', now()->month)
            ->orderBy('timestamp')
            ->get();
    
        $totalWorkDuration = 0;
        $totalRetard = 0;
        $totalAbsence = 0;
        $daysInCurrentMonth = Carbon::create(now()->year, now()->month, 1)->daysInMonth;
    
        for ($day = 1; $day <= $daysInCurrentMonth; $day++) {
            if (Carbon::create(now()->year, now()->month, $day)->isFuture()) {
                continue;
            }
    
            $dayMonthAttendances = $currentMonthAttendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->day == $day);
    
            if ($dayMonthAttendances->isEmpty()) {
                $totalAbsence++;
            } else {
                $firstAttendance = $dayMonthAttendances->first();
                if ($firstAttendance->status === 'late') $totalRetard++;
    
                $workDuration = 0;
                $arriveeTime = null;
    
                foreach ($dayMonthAttendances as $attendance) {
                    if ($attendance->type === 'arrivee') {
                        $arriveeTime = Carbon::parse($attendance->timestamp);
                    } elseif ($attendance->type === 'depart' && $arriveeTime) {
                        $workDuration += $arriveeTime->diffInMinutes(Carbon::parse($attendance->timestamp));
                        $arriveeTime = null;
                    }
                }
    
                // Si l'utilisateur est encore présent
                if ($arriveeTime && Carbon::parse($arriveeTime)->isSameDay(Carbon::now())) {
                    $workDuration += $arriveeTime->diffInMinutes(now());
                }
    
                $totalWorkDuration += $workDuration;
            }
        }
    
        // Calcul des données pour le jour actuel
        $todayWorkDuration = 0;
        $arriveeTime = null;
    
        foreach ($currentMonthAttendances as $attendance) {
            if ($attendance->type === 'arrivee' && Carbon::parse($attendance->timestamp)->isToday()) {
                $arriveeTime = Carbon::parse($attendance->timestamp);
            } elseif ($attendance->type === 'depart' && $arriveeTime) {
                $todayWorkDuration += $arriveeTime->diffInMinutes(Carbon::parse($attendance->timestamp));
                $arriveeTime = null;
            }
        }
    
        if ($arriveeTime) {
            $todayWorkDuration += $arriveeTime->diffInMinutes(now());
        }
    
        $todayWorkDurationFormatted = gmdate('H:i:s', $todayWorkDuration * 60);
    
        // Réponse JSON avec toutes les données calculées
        $return = [
            'status' => 200,
            'message' => __('success'),
            'result' => [
                'daily_data' => $dailyData,
                'total_work_duration' => ($totalWorkDuration > 0) ? gmdate('H:i:s', $totalWorkDuration * 60) : $todayWorkDurationFormatted,
                'total_retard' => $totalRetard,
                'total_absence' => $totalAbsence,
                'today_status' => $currentMonthAttendances->first(fn($a) => Carbon::parse($a->timestamp)->isToday())?->status ?? 'absent',
                'today_work_duration' => $todayWorkDurationFormatted
            ],
        ];
    
        return response()->json($return, $return['status']);
    }
    
    
    

    public function getLastSevenDaysStatus(Request $request)
    {
        // Validation de l'ID utilisateur
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',  // Vérification que l'ID utilisateur existe dans la table users
        ]);

        $userId = $validated['user_id'];  // Récupère l'ID de l'utilisateur envoyé dans la requête


        // Récupérer les données de présence pour les 7 derniers jours
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $attendances = Attendance::where('user_id', $userId)
            ->where('timestamp', '>=', $sevenDaysAgo)
            ->orderBy('timestamp')
            ->get();

        $dailyData = [];
        $todayData = [];

        // Récupérer les présences pour aujourd'hui
        $todayAttendances = $attendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->isToday());

        // Traitement des 7 derniers jours
        for ($i = 0; $i < 7; $i++) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayAttendances = $attendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->format('Y-m-d') == $day);
            
            if ($dayAttendances->isEmpty()) {
                $dailyData[$day] = ['status' => 'absent'];
            } else {
                $firstAttendance = $dayAttendances->first(); // Récupérer la première présence du jour
                $status = $firstAttendance->status;
                $dailyData[$day] = [
                    'status' => $status,
                ];
            }
        }

        // Traitement des données de la journée en cours
        if ($todayAttendances->isNotEmpty()) {
            $lastAttendance = $todayAttendances->last(); // Dernier pointage de la journée
            $todayData = [
                'date' => Carbon::today()->format('Y-m-d'),
                'status' => $todayAttendances->first()->status, // Statut basé sur le premier pointage de la journée
                'last_timestamp' => $lastAttendance->timestamp,
                'type' => $lastAttendance->type, // Type de pointage (arrivee ou depart)
            ];
        } else {
            // Si pas de pointage pour aujourd'hui, marquer comme absent
            $todayData = [
                'date' => Carbon::today()->format('Y-m-d'),
                'status' => 'absent',
                'last_timestamp' => null,
                'type' => null,
            ];
        }

        // Retourner la réponse JSON avec les données

        $return = [
            'status' => 200,
            'message' => __('success'),
            'result' => [],
        ];

        if ($dailyData && $todayData) {
            $return['result'] = [
                'seven_day_status' => $dailyData,
                'today_data' => $todayData,
            ];
        } else {
            // Si les données sont vides ou non autorisées
            $return['status'] = 401;
            $return['message'] = 'Non autorisé';
        }
    
        return response()->json($return, $return['status']);

        
    }


    
}
