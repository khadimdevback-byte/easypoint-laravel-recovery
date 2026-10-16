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

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $selectedSiteName = $request->input('type'); // Récupérer le nom du site sélectionné
        $period = $request->input('period', 'day');

        // Récupérer les sites de l'entreprise de l'utilisateur
        $sitesQuery = Site::with(['company', 'customer', 'controller', 'users'])
            ->where('company_id', Auth::user()->company_id);

        // Filtrer par nom du site
        if (!empty($selectedSiteName)) {
            $sitesQuery->where('name', 'like', "%$selectedSiteName%");
        }

        if ($search) {
            $sitesQuery->where('name', 'like', "%$search%");
        }

        $sites = $sitesQuery->paginate(25);

        // Récupération des utilisateurs liés aux sites
        $usersQuery = User::where('company_id', Auth::user()->company_id)
            ->where('user_type', 'user')
            ->where('type_horaire','fixe');

        // Vérifier si l'utilisateur est un contrôleur
        if (is_controller()) {
            $authorizeControllers = SiteControllerUser::where('user_id', Auth::user()->id)->get();
            $siteIds = $authorizeControllers->pluck('site_id');

            $usersQuery->whereIn('id', function ($query) use ($siteIds) {
                $query->select('user_id')
                    ->from('site_user')
                    ->whereIn('site_id', $siteIds);
            });
        }

        // Filtrer par site sélectionné (nom)
        if (!empty($selectedSiteName)) {
            $usersQuery->whereIn('id', function ($query) use ($selectedSiteName) {
                $query->select('user_id')
                    ->from('site_user')
                    ->whereIn('site_id', function ($subQuery) use ($selectedSiteName) {
                        $subQuery->select('id')
                            ->from('sites')
                            ->where('name', 'like', "%$selectedSiteName%");
                    });
            });
        }

        // Filtrer par recherche
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $usersQuery->paginate(25);

        // Récupérer les présences et les métriques
        $attendances = [];
        $metrics = [];
        $rawAttendances = collect();
        $date = Carbon::now();

        if ($period === 'day') {
            $date = $request->input('day', Carbon::now()->toDateString());
            $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
                ->whereDate('created_at', $date)
                ->get();
        } elseif ($period === 'week') {
            $date = $request->input('week', Carbon::now()->startOfWeek()->toDateString());
            $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
                ->whereBetween('created_at', [Carbon::parse($date)->startOfWeek(), Carbon::parse($date)->endOfWeek()])
                ->get();
        } elseif ($period === 'month') {
            $date = $request->input('month', Carbon::now()->format('Y-m'));
            $parsedDate = Carbon::createFromFormat('Y-m', $date);
            $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
                ->whereYear('created_at', $parsedDate->year)
                ->whereMonth('created_at', $parsedDate->month)
                ->get();
        } elseif ($period === 'year') {
            $date = $request->input('year', Carbon::now()->year);
            $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
                ->whereYear('created_at', $date)
                ->get();
        }

        // Organiser les présences
        foreach ($rawAttendances as $attendance) {
            $userId = $attendance->user_id;
            $day = Carbon::parse($attendance->timestamp)->toDateString();

            if (!isset($attendances[$userId])) {
                $attendances[$userId] = [];
            }

            if (!isset($attendances[$userId][$day])) {
                $attendances[$userId][$day] = [];
            }

            $attendances[$userId][$day][] = [
                'timestamp' => $attendance->timestamp,
                'type' => $attendance->type,
                'site' => $attendance->site,
                'status' => $attendance->status,
            ];
        }

        // Calcul des métriques
        foreach ($attendances as $userId => $days) {
            $totalLate = 0;
            $totalAbsent = 0;
            $totalPresent = 0;
            $totalHours = 0;
            $totalDaysInPeriod = $this->getTotalDaysInPeriod($period, $date);
            $daysWithAttendances = count($days);
            $totalAbsent = $totalDaysInPeriod - $daysWithAttendances;

            foreach ($days as $day => $records) {
                $dayHours = 0;
                $lastArrivalTime = null;

                // Trier les enregistrements par timestamp
                usort($records, function ($a, $b) {
                    return Carbon::parse($a['timestamp'])->timestamp <=> Carbon::parse($b['timestamp'])->timestamp;
                });

                // Calculer le temps de travail pour chaque paire arrivée/départ
                foreach ($records as $record) {
                    if ($record['type'] === 'arrivee') {
                        $lastArrivalTime = Carbon::parse($record['timestamp']);
                    } elseif ($record['type'] === 'depart' && $lastArrivalTime) {
                        $departureTime = Carbon::parse($record['timestamp']);
                        // Calculer en minutes puis convertir en heures décimales
                        $dayHours += $departureTime->diffInMinutes($lastArrivalTime) / 60;
                        $lastArrivalTime = null;
                    }
                }

                // Si la dernière entrée est une arrivée sans départ
                if ($lastArrivalTime) {
                    $now = Carbon::now();
                    $dayHours += $now->diffInMinutes($lastArrivalTime) / 60;
                }

                $totalHours += $dayHours;

                $firstRecord = $records[0];
                if ($firstRecord['status'] === 'late') {
                    $totalLate++;
                } elseif ($firstRecord['status'] === 'present') {
                    $totalPresent++;
                }
            }

            $metrics[$userId] = [
                'total_late' => $totalLate,
                'total_absent' => $totalAbsent,
                'total_present' => $totalPresent,
                'total_hours' => $this->convertDecimalToTime($totalHours),
            ];
        }

        // Ajouter la fonction pour chaque utilisateur
        foreach ($users as $user) {
            $user->employee_function = $user->fonction ? $user->fonction->nom_fonction : "";
        }

        return view('balance_sheet.index', compact('sites', 'users', 'metrics', 'selectedSiteName'));
    }




    public function superAdminIndex(Request $request)
    {
        $search = $request->input('search');

        $companies = Company::with('sites')->withCount([
            'users as managers_count' => function ($query) {
                $query->where('user_type', 'manager');
            },
            'users as controllers_count' => function ($query) {
                $query->where('user_type', 'controller');
            },
            'users as users_count' => function ($query) {
                $query->where('user_type', 'user');
            },
            'users as customers_count' => function ($query) {
                $query->where('user_type', 'customer');
            },
        ])
            ->when($search, function ($query, $search) {
                return $query->where('compagny_name', 'like', '%' . $search . '%');
            })
            ->paginate(25);

        // Retourner les métriques à la vue
        return view('balance_sheet.superadmin-index', compact('companies'));
    }


    private function getTotalDaysInPeriod($period, $date)
    {
        $startDate = Carbon::parse($date);

        if ($period === 'day') {
            return 1;
        } elseif ($period === 'week') {
            return $startDate->copy()->daysInWeek;
        } elseif ($period === 'month') {
            return $startDate->copy()->daysInMonth;
        } elseif ($period === 'year') {
            return $startDate->copy()->daysInYear;
        }

        return 0;
    }

    public function convertDecimalToTime($decimalTime)
    {
        // Prenez la valeur absolue du temps
        $decimalTime = abs($decimalTime);

        // Convertir tout en minutes d'abord
        $totalMinutes = round($decimalTime * 60);

        // Calculer les jours, heures, minutes
        $days = floor($totalMinutes / (24 * 60));
        $totalMinutes -= $days * 24 * 60;

        $hours = floor($totalMinutes / 60);
        $totalMinutes -= $hours * 60;

        $minutes = $totalMinutes;

        // Construction de la chaîne de sortie
        $parts = [];
        if ($days > 0) {
            $parts[] = $days . ' jour' . ($days > 1 ? 's' : '');
        }
        if ($hours > 0 || $days > 0) {
            $parts[] = $hours . ' h';
        }
        if ($minutes > 0 || $hours > 0 || $days > 0) {
            $parts[] = sprintf('%02d min', $minutes);
        }

        return implode(' ', $parts);
    }
}
