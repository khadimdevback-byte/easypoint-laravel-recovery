<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $manager = $request->user();
        abort_unless($manager?->user_type === 'manager', 403);
        abort_unless($manager->company_id, 403);

        $companyId = $manager->company_id;
        $employees = User::where('company_id', $companyId)
            ->where('user_type', 'user')
            ->where('status', 'active');
        $employeeCount = (clone $employees)->count();

        $todayAttendances = Attendance::where('company_id', $companyId)
            ->whereDate('timestamp', today())
            ->get(['user_id', 'status']);
        $presentUserIds = $todayAttendances
            ->whereIn('status', ['present', 'late'])
            ->pluck('user_id')
            ->unique();

        return view('manager.dashboard', [
            'manager' => $manager,
            'company' => $manager->company,
            'employeeCount' => $employeeCount,
            'controllerCount' => User::where('company_id', $companyId)
                ->where('user_type', 'controller')
                ->where('status', 'active')
                ->count(),
            'siteCount' => Site::where('company_id', $companyId)->count(),
            'presentCount' => $presentUserIds->count(),
            'lateCount' => $todayAttendances->where('status', 'late')->pluck('user_id')->unique()->count(),
            'absentCount' => max(0, $employeeCount - $presentUserIds->count()),
            'recentEmployees' => (clone $employees)
                ->orderBy('name')
                ->limit(5)
                ->get(['id', 'name', 'first_name', 'email']),
        ]);
    }
}
