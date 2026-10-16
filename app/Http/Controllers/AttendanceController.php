<?php

namespace App\Http\Controllers;

use App\Models\MissingDeparture;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SiteUser;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\SiteControllerUser;
use Illuminate\Support\Facades\Auth;
use App\Models\Site;
use App\Models\UserDeactivationReason;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $site = $request->input('site');
        $search = $request->input('search');
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        $sites = Site::where('sites.company_id', $user->company_id)->get();

        if (is_controller()) {
            $authorizeControllers = SiteControllerUser::where('user_id', Auth::user()->id)->get();
            $siteIds = $authorizeControllers->pluck('site_id');

            $users = User::whereIn('id', function ($query) use ($siteIds) {
                    $query->select('user_id')
                        ->from('site_user')
                        ->whereIn('site_id', $siteIds);
                })
                ->where('company_id', Auth::user()->company_id)
                ->where('user_type', 'user')
                ->when($site, function ($query) use ($site) {
                    $query->whereIn('id', function ($subQuery) use ($site) {
                        $subQuery->select('user_id')
                            ->from('site_user')
                            ->where('site_id', $site);
                    });
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })
                ->paginate(25);

            $rawAttendances = Attendance::whereIn('site_id', $siteIds)
                ->whereYear('timestamp', $year)
                ->whereMonth('timestamp', $month)
                ->when($site, function ($query) use ($site) {
                    $query->where('site_id', $site);
                })
                ->orderBy('timestamp')
                ->get();
        } else {
            $users = User::where('company_id', Auth::user()->company_id)
                ->where('user_type', 'user')
                ->when($site, function ($query) use ($site) {
                    $query->whereIn('id', function ($subQuery) use ($site) {
                        $subQuery->select('user_id')
                            ->from('site_user')
                            ->where('site_id', $site);
                    });
                })
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('first_name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
                })
                ->paginate(25);

            $rawAttendances = Attendance::where('company_id', Auth::user()->company_id)
                ->whereYear('timestamp', $year)
                ->whereMonth('timestamp', $month)
                ->when($site, function ($query) use ($site) {
                    $query->where('site_id', $site);
                })
                ->orderBy('timestamp')
                ->get();
        }

        $userIds = $users->items();

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $userLeaves = \App\Models\Demande::whereIn('user_id', array_column($userIds, 'id'))
            ->where('statut', 'approuve')
            ->where('date_debut', '<=', $endOfMonth)
            ->where('date_fin', '>=', $startOfMonth)
            ->get()
            ->groupBy('user_id');

        $attendances = [];
        foreach ($rawAttendances as $attendance) {
            $day = Carbon::parse($attendance->timestamp)->day;
            if (!isset($attendances[$attendance->user_id])) {
                $attendances[$attendance->user_id] = [];
            }
            if (!isset($attendances[$attendance->user_id][$day])) {
                $attendances[$attendance->user_id][$day] = [
                    'timestamp' => $attendance->timestamp,
                    'type'      => $attendance->type,
                    'status'    => $attendance->status,
                ];
            }
        }

        return view('attendances.index', compact(
            'users',
            'attendances',
            'year',
            'month',
            'daysInMonth',
            'site',
            'sites',
            'userLeaves'
        ));
    }

    public function show(Request $request, $userId)
    {
        $day = $request->input('day', now()->day);
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $user = User::findOrFail($userId);

        $attendances = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month)
            ->whereDay('timestamp', $day)
            ->orderBy('timestamp')
            ->get();

        $totalWorkDuration = 0;
        $totalRetard = 0;
        $lastArrivalTime = null;
        $lastDepartTime = null;
        $dailyWorkDurations = [];
        $expectedWorkDuration = 0;

        if ($attendances->isNotEmpty()) {
            $firstAttendance = $attendances->first();
            $timestamp = Carbon::parse($firstAttendance->timestamp);
            $dayOfWeek = strtolower($timestamp->format('l'));

            $userChecked = $user->{$dayOfWeek . '_checked'};
            $startTime = null;
            $endTime = null;

            if ($userChecked) {
                $startTime = $user->{'start_time_' . $dayOfWeek};
                $endTime = $user->{'end_time_' . $dayOfWeek};
            } else {
                $site = $firstAttendance->site;
                if ($site->{$dayOfWeek . '_checked'}) {
                    $startTime = $site->{'start_time_' . $dayOfWeek};
                    $endTime = $site->{'end_time_' . $dayOfWeek};
                }
            }

            if ($startTime && $endTime) {
                $startDateTime = Carbon::parse($startTime)->setDate($year, $month, $day);
                $endDateTime = Carbon::parse($endTime)->setDate($year, $month, $day);
                $expectedWorkDuration = $endDateTime->diffInMinutes($startDateTime);
            }
        }

        foreach ($attendances as $attendance) {
            $timestamp = Carbon::parse($attendance->timestamp);

            if ($attendance->type === 'arrivee') {
                $lastArrivalTime = $timestamp;
            } elseif ($attendance->type === 'depart') {
                $lastDepartTime = $timestamp;

                if ($lastArrivalTime) {
                    $workDuration = $lastArrivalTime->diffInMinutes($lastDepartTime);

                    $dailyWorkDurations[] = [
                        'start' => $lastArrivalTime,
                        'end' => $lastDepartTime,
                        'duration' => $workDuration,
                        'expected_duration' => $expectedWorkDuration
                    ];

                    $totalWorkDuration += $workDuration;
                    $lastArrivalTime = null;
                }
            }

            if ($attendance->type === 'arrivee') {
                if ($attendance->delay_in_minutes !== null) {
                    $totalRetard += $attendance->delay_in_minutes;
                }
            }
        }

        if ($lastArrivalTime && !$lastDepartTime) {
            $now = Carbon::now();
            $workDuration = $lastArrivalTime->diffInMinutes($now);

            $dailyWorkDurations[] = [
                'start' => $lastArrivalTime,
                'end' => $now,
                'duration' => $workDuration,
                'expected_duration' => $expectedWorkDuration
            ];

            $totalWorkDuration += $workDuration;
        }

        $totalPauseDuration = 0;
        for ($i = 1; $i < count($dailyWorkDurations); $i++) {
            $pauseDuration = $dailyWorkDurations[$i]['start']->diffInMinutes($dailyWorkDurations[$i - 1]['end']);
            $totalPauseDuration += $pauseDuration;
        }

        $formattedWorkDuration = gmdate('H:i:s', $totalWorkDuration * 60);
        $formattedPauseDuration = gmdate('H:i:s', $totalPauseDuration * 60);
        $formattedExpectedDuration = $expectedWorkDuration > 0 ? gmdate('H:i:s', $expectedWorkDuration * 60) : '00:00:00';

        $user->employee_function = $user->fonction ? $user->fonction->nom_fonction : "";

        return view('attendances.show', compact(
            'user',
            'attendances',
            'formattedWorkDuration',
            'formattedPauseDuration',
            'formattedExpectedDuration',
            'totalRetard',
            'dailyWorkDurations',
            'year',
            'month',
            'day'
        ));
    }

    /**
     * Sélectionner un site pour la borne
     */
    public function setBorneSite(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id'
        ]);

        $user = Auth::user();

        if (in_array($user->user_type, ['admin', 'super_admin'])) {
            $site = Site::where('id', $request->site_id)
                        ->where('company_id', $user->company_id)
                        ->first();
            $hasAccess = (bool) $site;
        } else {
            $hasAccess = DB::table('site_controller')
                ->where('user_id', $user->id)
                ->where('site_id', $request->site_id)
                ->exists();
            $site = Site::find($request->site_id);
        }

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce site.'
            ], 403);
        }

        cookie()->queue(cookie('selected_site_id', $request->site_id, 60 * 24 * 30));
        session(['selected_site_id' => $request->site_id]);

        return response()->json([
            'success' => true,
            'message' => 'Site sélectionné : ' . $site->name,
            'site_name' => $site->name
        ]);
    }

    /**
     * Afficher la borne de pointage
     */
    public function borneIndex($siteId = null)
    {
        $user = Auth::user();

        if (in_array($user->user_type, ['admin', 'super_admin'])) {
            $sites = Site::where('company_id', $user->company_id)
                        ->where('status', 'active')
                        ->get();
        } else {
            $siteIds = DB::table('site_controller')
                        ->where('user_id', $user->id)
                        ->pluck('site_id')
                        ->toArray();

            if (empty($siteIds)) {
                return redirect()->route('dashboard')
                    ->with('error', 'Aucun site associé à votre compte.');
            }

            $sites = Site::whereIn('id', $siteIds)
                        ->where('status', 'active')
                        ->get();
        }

        if ($siteId) {
            $selectedSite = $sites->find($siteId);
            if (!$selectedSite) {
                $first = $sites->first();
                if ($first) {
                    return redirect()->route('borne.index', ['siteId' => $first->id]);
                }
                return redirect()->route('dashboard')->with('error', 'Aucun site disponible.');
            }
            return view('attendances.borne', compact('sites', 'selectedSite'));
        }

        $selectedSite = null;

        if (session()->has('selected_site_id')) {
            $selectedSite = $sites->find(session('selected_site_id'));
        }

        if (!$selectedSite) {
            $selectedSite = $sites->first();
        }

        if ($selectedSite) {
            return redirect()->route('borne.index', ['siteId' => $selectedSite->id]);
        }

        return redirect()->route('dashboard')->with('error', 'Aucun site disponible.');
    }

    /**
     * Enregistrer un pointage par badge (avec gestion des pauses/reprises)
     */
    /**
     * Enregistrer un pointage par badge (avec gestion des pauses/reprises)
     */
    public function recordBadgeAttendance(Request $request)
    {
        try {
            $request->validate([
                'matricule' => 'required',
                'site_id' => 'required|exists:sites,id'
            ]);

            // Nettoyer le matricule scanné
            $matriculeScanne = null;
            if ($request->has('matricule')) {
                $matriculeScanne = trim($request->matricule);
                $matriculeScanne = preg_replace('/[\x00-\x1F\x7F-\x9F]/', '', $matriculeScanne);
                $matriculeScanne = preg_replace('/[\r\n\t]/', '', $matriculeScanne);
                $matriculeScanne = strtoupper($matriculeScanne);
                $matriculeScanne = preg_replace('/\s+/', ' ', $matriculeScanne);
            }

            // Rechercher l'utilisateur
            $user = null;

            if ($request->has('user_id')) {
                $user = User::find($request->user_id);
            } elseif ($matriculeScanne && strlen($matriculeScanne) > 0) {
                $user = User::where('matricule', $matriculeScanne)->first();

                if (!$user) {
                    $user = User::where('matricule', 'LIKE', '%' . $matriculeScanne . '%')->first();
                }

                if (!$user) {
                    $chiffresScannes = preg_replace('/[^0-9]/', '', $matriculeScanne);
                    $users = User::whereNotNull('matricule')->get();
                    foreach ($users as $u) {
                        $chiffresUser = preg_replace('/[^0-9]/', '', $u->matricule);
                        if ($chiffresUser == $chiffresScannes) {
                            $user = $u;
                            break;
                        }
                    }
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employé non trouvé. Vérifiez votre badge.'
                ], 404);
            }

            $site = Site::findOrFail($request->site_id);

            // Vérifications d'identité
            if ($user->company_id !== $site->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Employé non autorisé sur ce site.'
                ], 403);
            }

            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Votre compte est désactivé.'
                ], 403);
            }

            if ($user->contract_end_date && Carbon::parse($user->contract_end_date)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Votre contrat a expiré.'
                ], 403);
            }

            if ($user->type_horaire !== 'fixe') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous n\'avez pas d\'horaire fixe.'
                ], 403);
            }

            if ($site->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le site n\'est pas actif.'
                ], 403);
            }

            $authorizeUser = SiteUser::where('site_id', $site->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$authorizeUser) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous n\'êtes pas autorisé sur ce site.'
                ], 403);
            }

            // ========== ✅ CORRECTION DU FUSEAU HORAIRE ==========
            // Récupérer le fuseau horaire de l'entreprise
            $company = $site->company;
            $timezone = $company->timezone ?? 'UTC';

            // ✅ FORCER le fuseau horaire globalement
            date_default_timezone_set($timezone);

            // ✅ Récupérer l'heure actuelle avec le bon fuseau
            $currentTime = Carbon::now($timezone);

            // 🔍 LOG DE DÉBOGAGE
            Log::info('=== POINTAGE BADGE - FUSEAU HORAIRE ===', [
                'timezone_entreprise' => $timezone,
                'heure_serveur_UTC' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                'heure_fuseau' => $currentTime->format('Y-m-d H:i:s'),
                'site_id' => $site->id,
                'company_id' => $company->id,
                'user_id' => $user->id,
                'user_matricule' => $user->matricule
            ]);

            $dayOfWeek = strtolower($currentTime->format('l'));

            // Vérification autorisation travail du jour
            $userChecked = $user->{$dayOfWeek . '_checked'} ?? $site->{$dayOfWeek . '_checked'};
            if (!$userChecked) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous n\'êtes pas autorisé à travailler aujourd\'hui.'
                ], 403);
            }

            // Horaires du jour
            $startTimeStr = $user->{'start_time_' . $dayOfWeek} ?? $site->{'start_time_' . $dayOfWeek};
            $endTimeStr = $user->{'end_time_' . $dayOfWeek} ?? $site->{'end_time_' . $dayOfWeek};

            $startTime = $startTimeStr ? Carbon::parse($startTimeStr)->setDate($currentTime->year, $currentTime->month, $currentTime->day) : null;
            $endTime = $endTimeStr ? Carbon::parse($endTimeStr)->setDate($currentTime->year, $currentTime->month, $currentTime->day) : null;

            // ========== VÉRIFICATION DE LA PLAGE HORAIRE ==========
            if ($startTime && $endTime) {
                if ($currentTime->lt($startTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => "⏰ Le pointage n'est pas encore disponible. Le site ouvre à {$startTime->format('H:i')}. Heure actuelle: " . $currentTime->format('H:i')
                    ], 403);
                }

                if ($currentTime->gt($endTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => "⏰ Le pointage n'est plus disponible. Le site ferme à {$endTime->format('H:i')}. Heure actuelle: " . $currentTime->format('H:i')
                    ], 403);
                }
            }

            // ========== GESTION DES DÉPARTS MANQUANTS ==========
            $missingCheck = $this->handleMissingDeparture($user, $site, $currentTime);
            if ($missingCheck['blocked']) {
                return response()->json([
                    'success' => false,
                    'message' => $missingCheck['message'],
                    'requires_admin' => true
                ], 403);
            }

            // ========== DÉTERMINATION DU TYPE DE POINTAGE ==========
            $lastAttendanceToday = Attendance::where('user_id', $user->id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->latest('timestamp')
                ->first();

            $validTransitions = [
                'arrivee' => ['depart', 'pause'],
                'pause' => ['reprise'],
                'reprise' => ['depart', 'pause'],
                'depart' => ['arrivee']
            ];

            if (!$lastAttendanceToday) {
                $nextType = 'arrivee';
            } else {
                if ($lastAttendanceToday->type === 'pause') {
                    $pauseStart = Carbon::parse($lastAttendanceToday->timestamp);
                    $pauseDuration = $currentTime->diffInMinutes($pauseStart);

                    if ($pauseDuration > 60) {
                        return response()->json([
                            'success' => false,
                            'message' => '⏰ Votre pause dépasse 1h. Veuillez contacter votre administrateur.'
                        ], 403);
                    }

                    $nextType = 'reprise';
                } else {
                    $availableTypes = $validTransitions[$lastAttendanceToday->type] ?? ['arrivee'];
                    $nextType = $availableTypes[0] ?? 'arrivee';

                    if (in_array($lastAttendanceToday->type, ['arrivee', 'reprise'])) {
                        $lastArrival = Attendance::where('user_id', $user->id)
                            ->where('site_id', $site->id)
                            ->whereDate('timestamp', $currentTime->toDateString())
                            ->whereIn('type', ['arrivee', 'reprise'])
                            ->latest('timestamp')
                            ->first();

                        if ($lastArrival) {
                            $workDuration = $currentTime->diffInMinutes($lastArrival->timestamp);
                            if ($workDuration >= 240 && in_array('pause', $validTransitions[$lastAttendanceToday->type] ?? [])) {
                                $nextType = 'pause';
                            }
                        }
                    }
                }
            }

            // ========== VÉRIFICATION POUR LES ARRIVÉES ==========
            if ($nextType === 'arrivee') {
                $arrivalToday = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'arrivee')
                    ->exists();

                if ($arrivalToday) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous avez déjà pointé votre arrivée aujourd'hui."
                    ], 403);
                }

                $departureToday = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'depart')
                    ->exists();

                if ($departureToday) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous avez déjà pointé votre départ aujourd'hui. Vous ne pouvez plus pointer d'arrivée."
                    ], 403);
                }

                if ($startTime) {
                    $lateMarkDuration = $site->late_mark_duration ?? 0;
                    $lateMarkTime = $startTime->copy()->addMinutes($lateMarkDuration);

                    if ($currentTime->gt($lateMarkTime)) {
                        $status = 'late';
                        $delayInMinutes = intval($startTime->diffInMinutes($currentTime));
                        if ($delayInMinutes < 0) $delayInMinutes = 0;
                    } else {
                        $status = 'present';
                        $delayInMinutes = 0;
                    }
                } else {
                    $status = 'present';
                }
            }

            // ========== VÉRIFICATION POUR LES DÉPARTS ==========
            if ($nextType === 'depart') {
                if ($endTime && $currentTime->lt($endTime)) {
                    $attenteMinutes = $endTime->diffInMinutes($currentTime);
                    return response()->json([
                        'success' => false,
                        'message' => "⏰ Pointage départ non autorisé avant {$endTime->format('H:i')}. Patientez encore {$attenteMinutes} minutes."
                    ], 403);
                }

                $hasArrival = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'arrivee')
                    ->exists();

                if (!$hasArrival) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous devez pointer votre arrivée avant de pointer votre départ."
                    ], 403);
                }

                $departureToday = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'depart')
                    ->exists();

                if ($departureToday) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous avez déjà pointé votre départ aujourd'hui."
                    ], 403);
                }

                if ($endTime && $currentTime->gt($endTime)) {
                    $isLateDeparture = true;
                }
            }

            // ========== VÉRIFICATION POUR LES PAUSES ==========
            if ($nextType === 'pause') {
                $hasArrival = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->whereIn('type', ['arrivee', 'reprise'])
                    ->exists();

                if (!$hasArrival) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous devez pointer votre arrivée avant de prendre une pause."
                    ], 403);
                }

                $pauseInProgress = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'pause')
                    ->latest('timestamp')
                    ->first();

                if ($pauseInProgress) {
                    $pauseStart = Carbon::parse($pauseInProgress->timestamp);
                    $pauseDuration = $currentTime->diffInMinutes($pauseStart);

                    if ($pauseDuration < 60) {
                        return response()->json([
                            'success' => false,
                            'message' => "⏸️ Vous êtes déjà en pause depuis {$pauseDuration} minutes. Vous devez faire une reprise."
                        ], 403);
                    }
                }
            }

            // ========== VÉRIFICATION POUR LES REPRISES ==========
            if ($nextType === 'reprise') {
                $lastPause = Attendance::where('user_id', $user->id)
                    ->where('site_id', $site->id)
                    ->whereDate('timestamp', $currentTime->toDateString())
                    ->where('type', 'pause')
                    ->latest('timestamp')
                    ->first();

                if (!$lastPause) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Vous devez pointer votre pause avant de faire une reprise."
                    ], 403);
                }
            }

            // ========== ENREGISTREMENT DU POINTAGE ==========
            $attendanceData = [
                'user_id' => $user->id,
                'company_id' => $site->company_id,
                'site_id' => $site->id,
                'timestamp' => $currentTime,
                'type' => $nextType,
                'source' => 'badge',
                'delay_in_minutes' => $delayInMinutes ?? 0,
            ];

            if ($nextType === 'arrivee' && isset($status)) {
                $attendanceData['status'] = $status;
            }

            if ($nextType === 'arrivee' && $startTime) {
                $attendanceData['expected_arrival'] = $startTime->format('H:i:s');
            }

            $attendance = Attendance::create($attendanceData);

            if (class_exists(\App\Services\CalculHeuresFixesMensuellesService::class) && in_array($nextType, ['arrivee', 'depart'])) {
                app(\App\Services\CalculHeuresFixesMensuellesService::class)->calculerPourUtilisateur($user, $currentTime->month, $currentTime->year);
            }

            // ========== CONSTRUCTION DU MESSAGE ==========
            $messages = [
                'arrivee' => isset($status) && $status === 'late'
                    ? "⚠️ Bonjour {$user->first_name}, arrivée enregistrée avec " . ($delayInMinutes ?? 0) . " minutes de retard !"
                    : "✅ Bonjour {$user->first_name}, arrivée enregistrée à {$currentTime->format('H:i')} !",
                'depart' => isset($isLateDeparture) && $isLateDeparture
                    ? "⚠️ Au revoir {$user->first_name}, départ enregistré à {$currentTime->format('H:i')} (après l'heure) !"
                    : "✅ Au revoir {$user->first_name}, départ enregistré à {$currentTime->format('H:i')} !",
                'pause' => "⏸️ Pause débutée à {$currentTime->format('H:i')}",
                'reprise' => "▶️ Reprise enregistrée à {$currentTime->format('H:i')}"
            ];

            $message = $messages[$nextType] ?? 'Pointage enregistré avec succès.';

            // ========== ENVOI DES NOTIFICATIONS ==========
            if ($nextType === 'arrivee') {
                $admin = User::where('company_id', $user->company_id)->where('user_type', 'admin')->get();
                $managers = User::where('company_id', $user->company_id)->where('user_type', 'manager')->get();
                $controllersIds = SiteControllerUser::where('site_id', $site->id)->pluck('user_id');
                $controllers = User::where('company_id', $user->company_id)
                    ->where('status', 'active')
                    ->whereIn('id', $controllersIds)
                    ->get();
                $recipients = $admin->merge($managers)->merge($controllers);
                \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\UserPointedNotification($user, $currentTime));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'employee_name' => "{$user->first_name} {$user->name}",
                'type' => $nextType,
                'time' => $currentTime->format('H:i'),
                'status' => $status ?? null,
                'delay_minutes' => $delayInMinutes ?? 0,
                'is_late_departure' => $isLateDeparture ?? false,
                'available_types' => $validTransitions[$nextType] ?? ['arrivee'],
                'start_time' => $startTime ? $startTime->format('H:i') : null,
                'end_time' => $endTime ? $endTime->format('H:i') : null,
                'current_time' => $currentTime->format('H:i:s'),
                'timezone' => $timezone, // ✅ Ajout du fuseau horaire dans la réponse
                'is_within_working_hours' => true
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur pointage badge: ' . $e->getMessage());
            Log::error('Fichier: ' . $e->getFile());
            Log::error('Ligne: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur système: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gérer les départs manquants de la veille
     */
    private function handleMissingDeparture($user, $site, $currentTime)
    {
        $yesterday = $currentTime->copy()->subDay();
        $dayOfWeekYesterday = strtolower($yesterday->format('l'));

        // Vérifier si l'utilisateur a une arrivée hier sans départ
        $arrivalYesterday = Attendance::where('user_id', $user->id)
            ->where('site_id', $site->id)
            ->whereDate('timestamp', $yesterday->toDateString())
            ->where('type', 'arrivee')
            ->first();

        $departureYesterday = Attendance::where('user_id', $user->id)
            ->where('site_id', $site->id)
            ->whereDate('timestamp', $yesterday->toDateString())
            ->where('type', 'depart')
            ->exists();

        $hasMissingDeparture = $arrivalYesterday && !$departureYesterday;

        if ($hasMissingDeparture) {
            $pendingRequest = MissingDeparture::where('user_id', $user->id)
                ->where('site_id', $site->id)
                ->where('date', $yesterday->toDateString())
                ->where('status', 'pending')
                ->first();

            if (!$pendingRequest) {
                $endTimeStrYesterday = $user->{'end_time_' . $dayOfWeekYesterday} ?? $site->{'end_time_' . $dayOfWeekYesterday};
                $expectedDepart = $endTimeStrYesterday ? Carbon::parse($endTimeStrYesterday)->setDate($yesterday->year, $yesterday->month, $yesterday->day) : null;

                MissingDeparture::create([
                    'user_id' => $user->id,
                    'site_id' => $site->id,
                    'company_id' => $site->company_id,  
                    'date' => $yesterday->toDateString(),
                    'expected_depart_time' => $expectedDepart ? $expectedDepart->format('H:i:s') : null,
                    'status' => 'pending'
                ]);
                return [
                    'blocked' => true,
                    'message' => "⚠️ Vous n'avez pas pointé votre départ hier. Une demande de validation a été créée. L'administrateur validera votre présence."
                ];
            } else {
                // Demande déjà en attente, on bloque quand même mais avec un message différent
                return [
                    'blocked' => true,
                    'message' => "⚠️ Une demande de validation pour votre départ manquant d'hier est déjà en attente. Veuillez attendre la validation de l'administrateur."
                ];
            }
        }

        // Vérifier si l'utilisateur a un départ manquant d'avant-hier (plus ancien)
        $twoDaysAgo = $currentTime->copy()->subDays(2);
        $arrivalTwoDaysAgo = Attendance::where('user_id', $user->id)
            ->where('site_id', $site->id)
            ->whereDate('timestamp', $twoDaysAgo->toDateString())
            ->where('type', 'arrivee')
            ->first();

        $departureTwoDaysAgo = Attendance::where('user_id', $user->id)
            ->where('site_id', $site->id)
            ->whereDate('timestamp', $twoDaysAgo->toDateString())
            ->where('type', 'depart')
            ->exists();

        if ($arrivalTwoDaysAgo && !$departureTwoDaysAgo) {
            return [
                'blocked' => true,
                'message' => "⚠️ Vous avez un départ manquant depuis 2 jours. Veuillez contacter votre administrateur."
            ];
        }

        return ['blocked' => false];
    }
}
