<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\SiteControllerUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $roleDashboard = match ($user?->user_type) {
            'super_admin' => 'dashboard.superadmin',
            'manager' => 'manager.dashboard',
            'controller' => 'dashboard.controller',
            'user' => 'employee.dashboard',
            'customer' => 'customer.dashboard',
            default => null,
        };

        if ($roleDashboard !== null) {
            return redirect()->route($roleDashboard);
        }

        abort_unless($user?->user_type === 'admin', 403);

        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $companyId = Auth::user()->company_id;

        // Récupérer les différents types d'utilisateurs
        $managerCount = User::where('company_id', $companyId)
            ->where('user_type', 'manager')
            ->count();
        $controllerCount = User::where('company_id', $companyId)
            ->where('user_type', 'controller')
            ->count();
        $userCount = User::where('company_id', $companyId)
            ->where('user_type', 'user')
            ->count();
        $customerCount = User::where('company_id', $companyId)
            ->where('user_type', 'customer')
            ->count();
        $siteCount = Site::where('company_id', $companyId)
            ->count();

        // Récupérer les attendances pour aujourd'hui et hier
        $todayAttendances = Attendance::where('company_id', $companyId)
            ->whereDate('timestamp', $today)
            ->get();

        $yesterdayAttendances = Attendance::where('company_id', $companyId)
            ->whereDate('timestamp', $yesterday)
            ->get();

        // Initialiser les compteurs
        $onTimeToday = 0;
        $lateToday = 0;
        $absentToday = 0;
        $isActif = 0;

        $onTimeYesterday = 0;
        $lateYesterday = 0;
        $absentYesterday = 0;

        $users = User::where('company_id', $companyId)
            ->where('user_type', 'user')
            ->where('status', 'active')
            ->count();

        // Calcul des statuts d'aujourd'hui
        $presentUserIdsToday = [];
        foreach ($todayAttendances as $attendance) {
            $status = $attendance->status;

            if ($status === 'present') {
                $onTimeToday++;
            } elseif ($status === 'late') {
                $lateToday++;
            }

            if ($status != null) {
                $isActif++;
            }

            // Ajouter les user_id distincts pour les utilisateurs présents aujourd'hui
            $presentUserIdsToday[$attendance->user_id] = true;
        }

        // Calcul des absents d'aujourd'hui
        $absentToday = $users - count($presentUserIdsToday);

        // Calcul des statuts d'hier
        $presentUserIdsYesterday = [];
        foreach ($yesterdayAttendances as $attendance) {
            $status = $attendance->status;

            if ($status === 'present') {
                $onTimeYesterday++;
            } elseif ($status === 'late') {
                $lateYesterday++;
            }

            // Ajouter les user_id distincts pour les utilisateurs présents hier
            $presentUserIdsYesterday[$attendance->user_id] = true;
        }

        // Calcul des absents d'hier
        $absentYesterday = $users - count($presentUserIdsYesterday);

        // Calcul des différences en pourcentage

        $uniqueUsersYesterday = $yesterdayAttendances->pluck('user_id')->unique()->count();

        $lateDifference = $yesterdayAttendances->count() > 0 ?
            (($lateToday - $lateYesterday) / max(1, $lateYesterday)) * 100 :
            0;

        $absentDifference = $users > 0 ?
            (($absentToday - $absentYesterday) / max(1, $users)) * 100 :
            0;


        // Récupérer les photos des 4 premiers utilisateurs ayant pointé aujourd'hui
        $firstFiveUsers = $todayAttendances
            ->sortBy('timestamp')
            ->unique('user_id')
            ->take(4)
            ->map(function ($attendance) {
                return [
                    'user_id' => $attendance->user_id,
                    'name' => $attendance->name,
                    'avatar' => $attendance->user->avatar,
                ];
            });

        // Préparer les données pour le graphique
        $statusData = [
            'present' => $onTimeToday,
            'late' => $lateToday,
            'absent' => $absentToday,
        ];

        //========================


        //  $sevenDaysAgo = Carbon::now()->subDays(7);  // Date d'il y a 7 jours

        // Initialiser les compteurs globaux
        $onTimeSevenDays = 0;
        $lateSevenDays = 0;
        $absentSevenDays = 0;

        // Initialiser les utilisateurs qui ont pointé sur les 7 jours
        $presentUserIdsSevenDays = [];

        // Parcours des 7 derniers jours
        for ($i = 0; $i < 7; $i++) {
            $currentDay = Carbon::now()->subDays($i);
            $startOfDay = $currentDay->startOfDay();
            $endOfDay = $currentDay->endOfDay();

            // Récupérer les pointages pour le jour courant
            $dailyAttendances = Attendance::where('company_id', $companyId)
                ->whereBetween('timestamp', [$startOfDay, $endOfDay])
                ->get();

            // Traiter les présences et les retards pour ce jour
            $dailyPresentUserIds = [];
            foreach ($dailyAttendances as $attendance) {
                $status = $attendance->status;
                $userId = $attendance->user_id;

                if ($status === 'present') {
                    $onTimeSevenDays++;
                    $dailyPresentUserIds[] = $userId;
                } elseif ($status === 'late') {
                    $lateSevenDays++;
                    $dailyPresentUserIds[] = $userId;
                }
            }

            // Identifier les absents pour ce jour
            $allUsers = User::where('company_id', $companyId)
                ->where('user_type', 'user')
                ->pluck('id')
                ->toArray();

            $absentUserIds = array_diff($allUsers, $dailyPresentUserIds);
            $absentSevenDays += count($absentUserIds);

            // Ajouter les présents du jour aux présents sur 7 jours
            $presentUserIdsSevenDays = array_merge($presentUserIdsSevenDays, $dailyPresentUserIds);
            $presentUserIdsSevenDays = array_unique($presentUserIdsSevenDays);
        }


        //========================



        // Passer les données à la vue
        return view('dashboard', [
            'users' => $users,
            'onTimeToday' => $onTimeToday,
            'lateToday' => $lateToday,
            'absentToday' => $absentToday,
            'isActif' => $isActif,
            'lateDifference' => $lateDifference,
            'absentDifference' => $absentDifference,
            'firstFiveUsers' => $firstFiveUsers,
            'managerCount' => $managerCount,
            'controllerCount' => $controllerCount,
            'userCount' => $userCount,
            'customerCount' => $customerCount,
            'siteCount' => $siteCount,
            'statusData' => $statusData,
            'onTimeSevenDays' => $onTimeSevenDays,
            'lateSevenDays' => $lateSevenDays,
            'absentSevenDays' => $absentSevenDays,
        ]);
    }

    public function controllerDashboard(Request $request)
    {
        abort_unless($request->user()?->user_type === 'controller', 403);

        // Récupérer tous les sites auxquels l'utilisateur authentifié a accès
        $authorizeControllers = SiteControllerUser::where('user_id', Auth::user()->id);

        // Compter le nombre de sites
        $totalSite = $authorizeControllers->count();

        // Extraire les IDs des sites
        $siteIds = $authorizeControllers->pluck('site_id');

        // Récupérer les utilisateurs assignés à ces sites
        $users = User::whereIn('id', function ($query) use ($siteIds) {
            $query->select('user_id')
                ->from('site_user')
                ->whereIn('site_id', $siteIds);
        })->where('company_id', Auth::user()->company_id)
            ->where('user_type', 'user');

        // Compter le nombre total d'utilisateurs assignés
        $totalUser = $users->count();

        // Récupérer les IDs des utilisateurs assignés
        $assignedUserIds = $users->pluck('id');

        // Récupérer la date d'aujourd'hui
        $today = Carbon::today();

        // Compter les utilisateurs assignés ayant pointé aujourd'hui
        $totalUsersToday = Attendance::whereIn('site_id', $siteIds)
            ->whereDate('timestamp', $today) // Filtrer par la date d'aujourd'hui
            ->whereIn('user_id', $assignedUserIds) // Filtrer uniquement les utilisateurs assignés
            ->distinct('user_id') // Compter uniquement les utilisateurs distincts
            ->count('user_id');

        // Préparer les mois des 12 derniers mois
        $months = [];
        $presentData = [];
        $lateData = [];
        $absentData = [];

        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y'); // Format : "Jan 2024", "Fév 2024", etc.

            // Récupérer les présences pour le mois donné
            $attendances = Attendance::whereIn('site_id', $siteIds)
                ->whereIn('user_id', $assignedUserIds)
                ->whereYear('timestamp', $month->year)
                ->whereMonth('timestamp', $month->month)
                ->get();

            // Statuts pour chaque mois
            $attendanceStats = [
                'present' => [],
                'late' => [],
                'absent' => []
            ];

            foreach ($attendances as $attendance) {
                $status = $attendance->status; // Fonction pour déterminer le statut
                $attendanceStats[$status][] = $attendance->user_id;
            }

            // Calculer les totaux pour ce mois
            $presentData[] = count($attendanceStats['present']);
            $lateData[] = count($attendanceStats['late']);
            $absentData[] = count($attendanceStats['absent']);
        }
        $months = array_reverse($months);


        return view('dashboard_controller', [
            'totalUser' => $totalUser,
            'totalSite' => $totalSite,
            'totalUsersToday' => $totalUsersToday,
            'months' => $months,
            'presentData' => $presentData,
            'lateData' => $lateData,
            'absentData' => $absentData,
        ]);
    }


    public function superAdminDashboard(Request $request)
    {
        abort_unless($request->user()?->user_type === 'super_admin', 403);

        $totalSuperAdmin = User::where('user_type', 'super_admin')->count();
        $totalManager = User::where('user_type', 'manager')->count();
        $totalController = User::where('user_type', 'controller')->count();
        $totalUser = User::where('user_type', 'user')->count();
        $totalCustomer = User::where('user_type', 'customer')->count();
        $totalSite = Site::count();
        $totalCompany = Company::count();
        $totalActiveCompany = Company::where('status', 'active')->count();
        $totalInactiveCompany = Company::where('status', 'inactive')->count();
        $totalPendingCompany = Company::where('status', 'pending')->count();

        // 👇 AJOUTER LE CHIFFRE D'AFFAIRES TOTAL
        $totalRevenue = \App\Models\Payment::where('status', 'approved')->sum('amount');

        // Tableau pour les types d'utilisateurs
        $userTypes = ['Super Admins', 'Gestionnaires', 'Contrôleurs', 'Employés', 'Clients'];

        // Données pour le graphique des types d'utilisateurs
        $userCounts = [$totalSuperAdmin, $totalManager, $totalController, $totalUser, $totalCustomer];

        // Récupérer les sociétés créées durant les 12 derniers mois
        $companies = Company::selectRaw("
                EXTRACT(MONTH FROM created_at) AS month,
                EXTRACT(YEAR FROM created_at) AS year,
                COUNT(*) AS count
            ")
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupByRaw("
                EXTRACT(YEAR FROM created_at),
                EXTRACT(MONTH FROM created_at)
            ")
            ->orderByRaw('EXTRACT(YEAR FROM created_at) ASC')
            ->orderByRaw('EXTRACT(MONTH FROM created_at) ASC')
            ->get();

        // Générer les mois et leurs données correspondantes (Sociétés)
        $months = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('F');
            $year = $date->format('Y');

            $months[] = $monthName . ' ' . $year;

            $companyCount = $companies->firstWhere('month', $date->month)?->count ?? 0;
            $counts[] = $companyCount;
        }

        // ==========================================
        // 🌟 GESTION DES PAIEMENTS POUR LE SUPER ADMIN
        // ==========================================

        // 1. Liste des 10 derniers paiements avec les relations nécessaires
        $latestPayments = \App\Models\Payment::with(['company', 'subscription.plan'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 2. Préparation des données financières (Chiffre d'affaires des 6 derniers mois)
        $financialMonths = [];
        $revenues = [];

        // Logique de calcul améliorée
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $financialMonths[] = $date->translatedFormat('F Y');

            // Calculer la somme des paiements approuvés pour ce mois
            $totalForMonth = \App\Models\Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'approved')
                ->sum('amount');

            // Convertir en float pour éviter les problèmes de type
            $revenues[] = (float) $totalForMonth;
        }

        // 🔍 Logs de débogage
        Log::info('=== DÉBOGAGE DES PAIEMENTS SUPER ADMIN ===');
        Log::info('Total paiements approuvés:', ['total' => $totalRevenue]);

        return view('dashboard_superadmin', [
            'userTypes'            => $userTypes,
            'userCounts'           => $userCounts,
            'totalSuperAdmin'      => $totalSuperAdmin,
            'totalManager'         => $totalManager,
            'totalController'      => $totalController,
            'totalUser'            => $totalUser,
            'totalCustomer'        => $totalCustomer,
            'totalSite'            => $totalSite,
            'totalCompany'         => $totalCompany,
            'totalActiveCompany'   => $totalActiveCompany,
            'totalInactiveCompany' => $totalInactiveCompany,
            'totalPendingCompany'  => $totalPendingCompany,
            'months'               => $months,
            'counts'               => $counts,
            'latestPayments'       => $latestPayments,
            'financialMonths'      => $financialMonths,
            'revenues'             => $revenues,
            'totalRevenue'         => $totalRevenue, // 👈 AJOUTER CETTE LIGNE
            'debug_total_payments' => \App\Models\Payment::count(),
            'debug_approved_payments' => $totalRevenue > 0 ? \App\Models\Payment::where('status', 'approved')->count() : 0,
            'debug_has_payments' => $totalRevenue > 0
        ]);
    }


    public function paymentDetails(Request $request, $mode)
    {
        // Valider le mode
        $validModes = ['fedapay', 'manual', 'all'];
        if (!in_array($mode, $validModes)) {
            abort(404);
        }

        // Construire la requête
        $query = \App\Models\Payment::with(['company', 'subscription.plan'])
            ->where('status', 'approved');

        // Filtrer par mode
        if ($mode === 'fedapay') {
            $query->where('payment_method', 'mobile_money')
                ->orWhere('payment_method', 'FedaPay')
                ->orWhere('payment_method', 'Mobile Money');
        } elseif ($mode === 'manual') {
            $query->where('payment_method', 'Chèque / Espèces')
                ->orWhere('payment_method', 'Gratuit');
        }

        // Récupérer les paiements
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculer les totaux
        $totalAmount = $query->sum('amount');
        $totalCount = $query->count();

        // Statistiques par mode
        $stats = [
            'fedapay' => \App\Models\Payment::where('status', 'approved')
                ->where(function($q) {
                    $q->where('payment_method', 'mobile_money')
                    ->orWhere('payment_method', 'FedaPay')
                    ->orWhere('payment_method', 'Mobile Money');
                })->sum('amount'),
            'manual' => \App\Models\Payment::where('status', 'approved')
                ->where(function($q) {
                    $q->where('payment_method', 'Chèque / Espèces')
                    ->orWhere('payment_method', 'Gratuit');
                })->sum('amount'),
            'total' => \App\Models\Payment::where('status', 'approved')->sum('amount'),
        ];

        // Déterminer le titre et l'icône
        $pageInfo = [
            'fedapay' => [
                'title' => 'Paiements FedaPay',
                'icon' => 'fa-mobile-alt',
                'color' => 'purple',
                'badge' => 'FedaPay'
            ],
            'manual' => [
                'title' => 'Paiements Chèque / Espèces',
                'icon' => 'fa-money-bill-wave',
                'color' => 'gold',
                'badge' => 'Manuel'
            ],
            'all' => [
                'title' => 'Tous les paiements',
                'icon' => 'fa-credit-card',
                'color' => 'blue',
                'badge' => 'Tous'
            ]
        ];

        $currentInfo = $pageInfo[$mode] ?? $pageInfo['all'];

        return view('superadmin.payments.details', [
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'totalCount' => $totalCount,
            'stats' => $stats,
            'mode' => $mode,
            'pageInfo' => $currentInfo,
        ]);
    }
}
