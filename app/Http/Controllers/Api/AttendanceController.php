<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\SiteUser;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SiteControllerUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserPointedNotification;
use App\Services\CalculHeuresFixesMensuellesService;
use Illuminate\Support\Facades\{Auth, DB};

class AttendanceController extends Controller
{

    public function recordAttendance(Request $request)
    {
        $user = Auth::user();

        // Log de la requête reçue
        Log::info('📥 REQUÊTE POINTAGE REÇUE', [
            'user_id' => $request->user_id,
            'attendance_type' => $request->attendance_type,
            'qr_code' => $request->qr_code,
        ]);

        // ✅ Restriction sur type_horaire supprimée : un employé peut pointer
        // qu'il ait un horaire fixe, variable, ou aucun type_horaire renseigné.

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'qr_code' => 'required|string',
            'attendance_type' => 'nullable|string|in:arrivee,depart,pause,reprise',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // ✅ Empêcher l'usurpation d'identité : un utilisateur connecté ne peut
        // pointer que pour lui-même, jamais pour un autre user_id.
        if ((int) $request->user_id !== (int) $user->id) {
            Log::warning('🚫 Tentative de pointage pour un autre utilisateur', [
                'auth_user_id' => $user->id,
                'requested_user_id' => $request->user_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pointer que votre propre présence.'
            ], 403);
        }

        // Vérifiez si le QR code existe
        $site = Site::where('qr_code', $request->qr_code)->first();
        $user = User::findOrFail($request->user_id);

        if (!$site) {
            return response()->json(['success' => false, 'message' => 'Code QR invalide.'], 404);
        }

        // Récupérer le fuseau horaire de l'entreprise
        $company = $site->company;
        $timezone = $company->timezone ?? 'UTC';

        // Convertir l'heure actuelle au fuseau horaire de l'entreprise
        $currentTime = Carbon::now($timezone);

        if ($site->status != 'active') {
            return response()->json(['success' => false, 'message' => 'Ce site n\'est pas actif.'], 403);
        }

        $authorizeUser = SiteUser::where('site_id', $site->id)->where('user_id', $request->user_id)->first();

        if (!$authorizeUser) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé sur ce site'], 403);
        }

        // Déterminer le jour de la semaine
        $dayOfWeek = strtolower($currentTime->format('l'));

        // Vérifier si l'utilisateur est autorisé à travailler ce jour-là
        $userChecked = $user->{$dayOfWeek . '_checked'};

        if (!$userChecked) {
            $userChecked = $site->{$dayOfWeek . '_checked'};
        }

        if (!$userChecked) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à vous connecter à ce site aujourd\'hui'], 403);
        }

        // Vérification de la distance
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $site->latitude, $site->longitude);
        if ($distance > $site->radius) {
            return response()->json(['success' => false, 'message' => 'Vous êtes en dehors de la zone désignée.'], 403);
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

        // ========== Récupérer le dernier pointage du jour ==========
        $latestAttendance = Attendance::where('user_id', $request->user_id)
            ->where('site_id', $site->id)
            ->whereDate('timestamp', $currentTime->toDateString())
            ->latest('timestamp')
            ->first();

        // ========== VÉRIFICATIONS SPÉCIFIQUES ==========
        $requestedType = $request->attendance_type;

        // ✅ Log du type demandé
        Log::info('📊 Type demandé: ' . $requestedType);

        // 1. VÉRIFICATION POUR LA PAUSE
        if ($requestedType === 'pause') {
            Log::info('🔍 Vérification pour PAUSE');

            // Vérifier qu'il y a une arrivée aujourd'hui
            $hasArrivalToday = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'arrivee')
                ->exists();

            if (!$hasArrivalToday) {
                Log::warning('❌ Pas d\'arrivée avant la pause');
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous devez d\'abord pointer votre arrivée avant de faire une pause.'
                ], 403);
            }

            // Vérifier qu'il n'y a pas déjà une pause en cours
            $pauseInProgress = Attendance::where('user_id', $request->user_id)
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

            // Vérifier qu'il n'y a pas déjà un départ aujourd'hui
            $hasDepartureToday = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'depart')
                ->exists();

            if ($hasDepartureToday) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà pointé votre départ aujourd\'hui. Vous ne pouvez plus faire de pause.'
                ], 403);
            }

            Log::info('✅ Pause autorisée');
        }

        // 2. VÉRIFICATION POUR LA REPRISE
        if ($requestedType === 'reprise') {
            Log::info('🔍 Vérification pour REPRISE');

            $lastPause = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'pause')
                ->latest('timestamp')
                ->first();

            if (!$lastPause) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous devez d\'abord pointer votre pause avant de faire une reprise.'
                ], 403);
            }

            Log::info('✅ Reprise autorisée');
        }

        // 3. VÉRIFICATION POUR LE DÉPART
        if ($requestedType === 'depart') {
            Log::info('🔍 Vérification pour DÉPART');

            // Vérifier qu'il y a une arrivée aujourd'hui
            $hasArrivalToday = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'arrivee')
                ->exists();

            if (!$hasArrivalToday) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous devez d\'abord pointer votre arrivée avant de pointer votre départ.'
                ], 403);
            }

            // Vérifier qu'il n'y a pas déjà un départ aujourd'hui
            $hasDepartureToday = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'depart')
                ->exists();

            if ($hasDepartureToday) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà pointé votre départ aujourd\'hui.'
                ], 403);
            }

            // ✅ Interdire le départ si le dernier pointage est une pause :
            // il faut d'abord pointer la reprise (chaîne stricte arrivee -> pause -> reprise -> depart)
            if ($latestAttendance && $latestAttendance->type === 'pause') {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous êtes en pause. Vous devez d\'abord pointer votre reprise avant de pointer votre départ.'
                ], 403);
            }

            // Vérification de l'heure de fin UNIQUEMENT pour le départ
            $endTime = $user->{'end_time_' . $dayOfWeek};
            if (!$endTime) {
                $endTime = $site->{'end_time_' . $dayOfWeek};
            }

            if ($endTime) {
                $endDateTime = Carbon::parse($endTime)->setDate(
                    $currentTime->year,
                    $currentTime->month,
                    $currentTime->day
                );

                if ($currentTime->lt($endDateTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => "⏰ Vous ne pouvez pas pointer votre départ avant {$endTime}. Heure actuelle: " . $currentTime->format('H:i:s')
                    ], 403);
                }
            }

            Log::info('✅ Départ autorisé');
        }

        // ============================================================
        // ✅ DÉTERMINATION DU TYPE DE POINTAGE - CORRECTION ICI
        // ============================================================
        // Si le type est spécifié dans la requête, on l'utilise
        if ($requestedType) {
            $nextType = $requestedType;
            Log::info('✅ Type utilisé (depuis requête): ' . $nextType);
        } else {
            // Si aucun type n'est spécifié, on détermine automatiquement
            if (!$latestAttendance) {
                $nextType = 'arrivee';
            } else {
                $validTransitions = [
                    'arrivee' => ['depart', 'pause'],
                    'pause' => ['reprise'],
                    'reprise' => ['depart', 'pause'],
                    'depart' => ['arrivee']
                ];

                $availableTypes = $validTransitions[$latestAttendance->type] ?? ['arrivee'];
                $nextType = $availableTypes[0] ?? 'arrivee';
            }
            Log::info('✅ Type utilisé (auto): ' . $nextType);
        }

        // ========== VÉRIFICATION SI DÉJÀ POINTÉ AUJOURD'HUI POUR L'ARRIVÉE ==========
        if ($nextType === 'arrivee') {
            $hasArrivalToday = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'arrivee')
                ->exists();

            if ($hasArrivalToday) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà pointé votre arrivée aujourd\'hui.'
                ], 403);
            }
        }

        $expectedArrival = null;
        $delayInMinutes = null;
        $status = null;

        // Calcul du retard uniquement pour l'arrivée
        if ($nextType == 'arrivee') {
            $status = $this->getAttendanceStatus($currentTime, $user, $site);

            $startTime = $user->{'start_time_' . $dayOfWeek};

            if (!$startTime) {
                $startTime = $site->{'start_time_' . $dayOfWeek};
            }

            if ($startTime) {
                $expectedArrival = Carbon::parse($startTime)->setDate(
                    $currentTime->year,
                    $currentTime->month,
                    $currentTime->day
                );

                if ($currentTime->gt($expectedArrival)) {
                    $delayInMinutes = $expectedArrival->diffInMinutes($currentTime, false);
                } else {
                    $delayInMinutes = 0;
                }
            }
        }

        // ========== ENREGISTREMENT DU POINTAGE ==========
        try {
            $attendance = Attendance::create([
                'user_id' => $request->user_id,
                'company_id' => $site->company_id,
                'site_id' => $site->id,
                'timestamp' => $currentTime,
                'type' => $nextType,
                'status' => $status ?? null,
                'expected_arrival' => $expectedArrival ? $expectedArrival->format('H:i:s') : null,
                'delay_in_minutes' => $delayInMinutes ?? null,
                'source' => 'mobile',
            ]);

            Log::info('✅ Pointage enregistré', [
                'user_id' => $request->user_id,
                'type' => $nextType,
                'requested_type' => $requestedType,
                'time' => $currentTime->format('H:i:s')
            ]);

            // Calculer heures mensuelles pour tous les utilisateurs (avec ou sans type_horaire),
            // uniquement pour arrivée/départ
            if (in_array($nextType, ['arrivee', 'depart'])) {
                $mois = $currentTime->month;
                $annee = $currentTime->year;
                app(CalculHeuresFixesMensuellesService::class)->calculerPourUtilisateur($user, $mois, $annee);
            }

            // Envoi des notifications (uniquement pour la première arrivée)
            $isFirstArrival = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $site->id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->where('type', 'arrivee')
                ->count() == 1;

            if ($isFirstArrival && $nextType === 'arrivee') {
                $admin = User::where('company_id', $user->company_id)
                    ->where('user_type', "admin")
                    ->get();

                $managers = User::where('company_id', $user->company_id)
                    ->where('user_type', "manager")
                    ->get();

                $controllersIds = SiteControllerUser::where('site_id', $site->id)
                    ->pluck('user_id');
                $controllers = User::where('company_id', $user->company_id)
                    ->where('status', 'active')
                    ->whereIn('id', $controllersIds)
                    ->get();

                $recipients = $admin->merge($managers)->merge($controllers);
                Notification::send($recipients, new UserPointedNotification($user, $currentTime));
            }

            // Message personnalisé selon le type
            $messages = [
                'arrivee' => '✅ Arrivée enregistrée avec succès.',
                'depart' => '✅ Départ enregistré avec succès.',
                'pause' => '⏸️ Pause débutée à ' . $currentTime->format('H:i:s'),
                'reprise' => '▶️ Reprise enregistrée à ' . $currentTime->format('H:i:s')
            ];

            return response()->json([
                'success' => true,
                'message' => $messages[$nextType] ?? 'Pointage enregistré avec succès.',
                'attendance' => $attendance,
                'attendance_type' => $nextType
            ], 201);

        } catch (\Exception $e) {
            Log::error('❌ Erreur API pointage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'enregistrement du pointage. Veuillez réessayer plus tard.'
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
            $pendingRequest = \App\Models\MissingDeparture::where('user_id', $user->id)
                ->where('site_id', $site->id)
                ->where('date', $yesterday->toDateString())
                ->where('status', 'pending')
                ->first();

            if (!$pendingRequest) {
                $endTimeStrYesterday = $user->{'end_time_' . $dayOfWeekYesterday} ?? $site->{'end_time_' . $dayOfWeekYesterday};
                $expectedDepart = $endTimeStrYesterday ? Carbon::parse($endTimeStrYesterday)->setDate($yesterday->year, $yesterday->month, $yesterday->day) : null;

                \App\Models\MissingDeparture::create([
                    'user_id' => $user->id,
                    'site_id' => $site->id,
                    'company_id' => $site->company_id,
                    'date' => $yesterday->toDateString(),
                    'expected_depart_time' => $expectedDepart ? $expectedDepart->format('H:i:s') : null,
                    'status' => 'pending'
                ]);

                return [
                    'blocked' => true,
                    'message' => "⚠️ Vous n'avez pas pointé votre départ hier. Une demande de validation a été créée."
                ];
            }
        }

        return ['blocked' => false];
    }

    /**
     * Calculer la distance entre deux coordonnées GPS
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceInKm = $earthRadius * $c;
        return $distanceInKm * 1000;
    }

    /**
     * Obtenir le statut du pointage (présent/retard)
     */
    private function getAttendanceStatus($timestamp, $user, $site)
    {
        $attendanceTime = Carbon::parse($timestamp);
        $dayOfWeek = strtolower($attendanceTime->format('l'));

        $userChecked = $user->{$dayOfWeek . '_checked'};
        $startTime = $user->{'start_time_' . $dayOfWeek};

        if (!$userChecked) {
            $userChecked = $site->{$dayOfWeek . '_checked'};
            $startTime = $site->{'start_time_' . $dayOfWeek};
        }

        if (!$userChecked || !$startTime) {
            return 'present';
        }

        $startTime = Carbon::parse($startTime)
            ->setDate($attendanceTime->year, $attendanceTime->month, $attendanceTime->day);

        $lateMarkDuration = $site->late_mark_duration ?? 0;
        $lateMarkTime = $startTime->copy()->addMinutes($lateMarkDuration);

        if ($attendanceTime->lte($lateMarkTime)) {
            return 'present';
        } elseif ($attendanceTime->gt($lateMarkTime)) {
            return 'late';
        }
    }

    /**
     * Récupérer tous les pointages de l'utilisateur connecté
     */
    public function getUserPointages(Request $request)
    {
        $user = Auth::user();

        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('timestamp', 'desc')
            ->get();

        $pointages = [];
        foreach ($attendances as $attendance) {
            $pointages[] = [
                'id' => $attendance->id,
                'type' => $attendance->type,
                'date' => $attendance->timestamp,
                'status' => $attendance->status,
                'delay_in_minutes' => $attendance->delay_in_minutes,
                'site_id' => $attendance->site_id,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $pointages
        ]);
    }

    /**
     * Récupérer les pointages d'un jour spécifique
     */
    public function getPointagesByDay(Request $request, $userId, $year, $month, $day)
    {
        $attendances = Attendance::where('user_id', $userId)
            ->whereYear('timestamp', $year)
            ->whereMonth('timestamp', $month)
            ->whereDay('timestamp', $day)
            ->orderBy('timestamp')
            ->get();

        $pointages = [];
        $totalWorkMinutes = 0;
        $lastArrival = null;
        $totalPauseMinutes = 0;
        $lastPauseStart = null;

        foreach ($attendances as $attendance) {
            $pointages[] = [
                'id' => $attendance->id,
                'type' => $attendance->type,
                'timestamp' => $attendance->timestamp,
                'status' => $attendance->status,
                'delay_in_minutes' => $attendance->delay_in_minutes,
            ];

            // Calcul de la durée de travail (en excluant les pauses)
            if ($attendance->type === 'arrivee') {
                $lastArrival = Carbon::parse($attendance->timestamp);
            } elseif ($attendance->type === 'depart' && $lastArrival) {
                $totalWorkMinutes += $lastArrival->diffInMinutes(Carbon::parse($attendance->timestamp));
                $lastArrival = null;
            } elseif ($attendance->type === 'pause') {
                if ($lastArrival) {
                    $totalWorkMinutes += $lastArrival->diffInMinutes(Carbon::parse($attendance->timestamp));
                    $lastArrival = null;
                }
                $lastPauseStart = Carbon::parse($attendance->timestamp);
            } elseif ($attendance->type === 'reprise') {
                $lastArrival = Carbon::parse($attendance->timestamp);
                if ($lastPauseStart) {
                    $totalPauseMinutes += $lastPauseStart->diffInMinutes(Carbon::parse($attendance->timestamp));
                    $lastPauseStart = null;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $pointages,
            'total_work_minutes' => $totalWorkMinutes,
            'total_pause_minutes' => $totalPauseMinutes,
            'formatted_work_duration' => gmdate('H:i:s', $totalWorkMinutes * 60),
            'formatted_pause_duration' => gmdate('H:i:s', $totalPauseMinutes * 60)
        ]);
    }
}
