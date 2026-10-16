<?php

namespace App\Http\Controllers\Employee;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeBilanController extends Controller
{
public function index(Request $request)
{
    // 1. Validation
    $validated = $request->validate([
        'year' => 'nullable|integer|min:2000|max:' . now()->year,
        'month' => 'nullable|integer|min:1|max:12',
    ]);

    // 2. Paramètres
    $userId = $request->user()->id;
    $year = $validated['year'] ?? now()->year;
    $month = $validated['month'] ?? now()->month;

    // 3. Présences du mois demandé
    $attendances = Attendance::where('user_id', $userId)
        ->whereYear('timestamp', $year)
        ->whereMonth('timestamp', $month)
        ->orderBy('timestamp')
        ->get();

    $dailyData = $this->buildDailyStatusData($attendances, $year, $month);

    if ($request->ajax()) {
        return response()->json([
            'daily_data' => $dailyData
        ]);
    }

    return view('employee.bilan', [
        'daily_data' => $dailyData,
    ]);
}



    private function buildDailyStatusData($attendances, $year, $month)
    {
        $statusMap = [];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            if ($date->isFuture()) continue;

            $dayAttendances = $attendances->filter(fn($a) => Carbon::parse($a->timestamp)->day === $day);

            $status = $dayAttendances->isEmpty()
                ? 'absent'
                : $dayAttendances->first()->status;

            $statusMap[$date->format('Y-m-d')] = $status;
        }

        return $statusMap;
    }




}
