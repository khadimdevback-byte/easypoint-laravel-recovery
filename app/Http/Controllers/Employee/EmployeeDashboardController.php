<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            // Si pas d'utilisateur connecté, redirige ou abort
            abort(403, 'Utilisateur non connecté');
        }
        abort_unless($user->user_type === 'user', 403);

        $userId = $user->id;

        // Dates et données
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $attendances = Attendance::where('user_id', $userId)
            ->where('timestamp', '>=', $sevenDaysAgo)
            ->orderBy('timestamp')
            ->get();

        $dailyData = [];

        // Traitement des 7 derniers jours
        for ($i = 0; $i < 7; $i++) {
            $day = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayAttendances = $attendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->format('Y-m-d') == $day);

            if ($dayAttendances->isEmpty()) {
                $dailyData[$day] = ['status' => 'absent'];
            } else {
                $firstAttendance = $dayAttendances->first();
                $dailyData[$day] = [
                    'status' => $firstAttendance->status,
                ];
            }
        }

        // Données pour aujourd'hui
        $todayAttendances = $attendances->filter(fn($attendance) => Carbon::parse($attendance->timestamp)->isToday());

        if ($todayAttendances->isNotEmpty()) {
            $lastAttendance = $todayAttendances->last();
            $todayData = [
                'date' => Carbon::today()->format('Y-m-d'),
                'status' => $todayAttendances->first()->status,
                'last_timestamp' => Carbon::parse($lastAttendance->timestamp)->format('H:i'),
                'type' => $lastAttendance->type,
            ];
        } else {
            $todayData = [
                'date' => Carbon::today()->format('Y-m-d'),
                'status' => 'absent',
                'last_timestamp' => null,
                'type' => null,
            ];
        }

        //dd($todayData);

        //Bilan

        // 1. Paramètres
        $userId = $request->user()->id;

        // 2. Présences du mois actuel
        $currentYear = now()->year;
        $currentMonth = now()->month;

        $currentMonthAttendances = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $currentYear)
            ->whereMonth('timestamp', $currentMonth)
            ->orderBy('timestamp')
            ->get();

        // 3. Statistiques du mois en cours
        [$totalWorkDuration, $totalRetard, $totalAbsence] = $this->computeMonthlyStats($currentMonthAttendances, $currentYear, $currentMonth);
        $todayWorkDurationFormatted = $this->computeTodayDurationFormatted($currentMonthAttendances);




        // Envoi des données à la vue
        return view('employee.dashboard', [
            'seven_day_status' => array_reverse($dailyData, true),
            'today_data' => $todayData,
            'user' => $request->user(),
            'total_work_duration' => $totalWorkDuration > 0
                ? $this->formatDurationFromMinutes($totalWorkDuration)
                : $todayWorkDurationFormatted,
            'total_retard' => $totalRetard,
            'total_absence' => $totalAbsence,
            'today_status' => $currentMonthAttendances->first(fn($a) => \Carbon\Carbon::parse($a->timestamp)->isToday())?->status ?? 'absent',
            'today_work_duration' => $todayWorkDurationFormatted,
        ]);
    }


    private function computeMonthlyStats($attendances, $year, $month)
    {
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $totalWorkDuration = $totalRetard = $totalAbsence = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            if ($date->isFuture()) continue;

            $dayAttendances = $attendances->filter(fn($a) => Carbon::parse($a->timestamp)->day === $day);

            if ($dayAttendances->isEmpty()) {
                $totalAbsence++;
                continue;
            }

            if ($dayAttendances->first()->status === 'late') {
                $totalRetard++;
            }

            $workDuration = 0;
            $arriveeTime = null;

            foreach ($dayAttendances as $a) {
                if ($a->type === 'arrivee') {
                    $arriveeTime = Carbon::parse($a->timestamp);
                } elseif ($a->type === 'depart' && $arriveeTime) {
                    $workDuration += $arriveeTime->diffInMinutes(Carbon::parse($a->timestamp));
                    $arriveeTime = null;
                }
            }

            if ($arriveeTime && $arriveeTime->isSameDay(now())) {
                $workDuration += $arriveeTime->diffInMinutes(now());
            }

            $totalWorkDuration += $workDuration;
        }

        return [$totalWorkDuration, $totalRetard, $totalAbsence];
    }


    function formatDurationFromMinutes($minutes): string
    {
        $totalSeconds = $minutes * 60;

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02dh %02dmin %02ds', $hours, $minutes, $seconds);
    }


    private function computeTodayDurationFormatted($attendances)
    {
        $todayWorkDuration = 0;
        $arriveeTime = null;

        foreach ($attendances as $a) {
            $timestamp = Carbon::parse($a->timestamp);
            if ($a->type === 'arrivee' && $timestamp->isToday()) {
                $arriveeTime = $timestamp;
            } elseif ($a->type === 'depart' && $arriveeTime) {
                $todayWorkDuration += $arriveeTime->diffInMinutes($timestamp);
                $arriveeTime = null;
            }
        }

        if ($arriveeTime) {
            $todayWorkDuration += $arriveeTime->diffInMinutes(now());
        }

        // Total en secondes
        $totalSeconds = $todayWorkDuration * 60;
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        // Format : 05h 04min 02s
        return sprintf('%02dh %02dmin %02ds', $hours, $minutes, $seconds);
    }
}
