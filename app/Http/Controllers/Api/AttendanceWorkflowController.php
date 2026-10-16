<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceWorkflow;
use App\Http\Controllers\Controller;

class AttendanceWorkflowController extends Controller
{
    /**
     * Ajouter ou mettre à jour le dernier type de pointage
     */
    public function addOrUpdatelastAttendanceType(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'nullable|string|in:arrivee,depart,pause,reprise',
            'site_id' => 'required|integer|exists:sites,id',
        ]);

        // Vérifier les transitions valides
        $lastAttendance = Attendance::where('user_id', $request->user_id)
            ->where('site_id', $request->site_id)
            ->latest('timestamp')
            ->first();

        if ($request->has('type') && $request->type) {
            $validTransitions = [
                'arrivee' => ['depart', 'pause'],
                'pause' => ['reprise'],
                'reprise' => ['depart', 'pause'],
                'depart' => ['arrivee']
            ];

            if ($lastAttendance && !in_array($request->type, $validTransitions[$lastAttendance->type] ?? [])) {
                $availableTypes = implode(', ', $validTransitions[$lastAttendance->type] ?? ['arrivee']);
                return response()->json([
                    'status' => 422,
                    'message' => "⚠️ Transition invalide. De '{$lastAttendance->type}', vous pouvez seulement: {$availableTypes}",
                    'available_types' => $validTransitions[$lastAttendance->type] ?? ['arrivee']
                ], 422);
            }
        }

        $latestAttendance = AttendanceWorkflow::where('user_id', $request->user_id)->first();

        if ($latestAttendance) {
            if ($request->has('type')) {
                $latestAttendance->type = $request->type;
                $latestAttendance->save();
            }
        } else {
            $attendanceWorkflow = new AttendanceWorkflow([
                'user_id' => $request->user_id,
                'type' => $request->type,
            ]);
            $attendanceWorkflow->save();
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
        ]);
    }

    /**
     * Récupérer le dernier type de pointage
     */
    public function getLastAttendanceType(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'site_id' => 'nullable|integer|exists:sites,id',
        ]);

        $query = Attendance::where('user_id', $request->user_id);
        
        if ($request->has('site_id')) {
            $query->where('site_id', $request->site_id);
        }
        
        $latestAttendance = $query->latest('timestamp')->first();

        $validTransitions = [
            'arrivee' => ['depart', 'pause'],
            'pause' => ['reprise'],
            'reprise' => ['depart', 'pause'],
            'depart' => ['arrivee']
        ];

        if ($latestAttendance) {
            $type = $latestAttendance->type;
            $availableTypes = $validTransitions[$type] ?? ['arrivee'];
            
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'result' => [
                    'latest_attendance_type' => $type,
                    'available_types' => $availableTypes,
                    'last_attendance_time' => $latestAttendance->timestamp,
                    'site_id' => $latestAttendance->site_id
                ],
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'result' => [
                    'latest_attendance_type' => 'initial',
                    'available_types' => ['arrivee'],
                    'last_attendance_time' => null,
                    'site_id' => null
                ],
            ]);
        }
    }

    /**
     * Vérifier le statut actuel (pause ou non)
     */
    public function getCurrentStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'site_id' => 'required|integer|exists:sites,id',
        ]);

        $currentTime = Carbon::now();
        
        $lastAttendance = Attendance::where('user_id', $request->user_id)
            ->where('site_id', $request->site_id)
            ->latest('timestamp')
            ->first();

        $isOnPause = false;
        $pauseDuration = 0;
        $pauseMaxReached = false;

        if ($lastAttendance && $lastAttendance->type === 'pause') {
            $pauseStart = Carbon::parse($lastAttendance->timestamp);
            $pauseDuration = $currentTime->diffInMinutes($pauseStart);
            $isOnPause = true;
            
            if ($pauseDuration > 60) {
                $pauseMaxReached = true;
            }
        }

        $validTransitions = [
            'arrivee' => ['depart', 'pause'],
            'pause' => ['reprise'],
            'reprise' => ['depart', 'pause'],
            'depart' => ['arrivee']
        ];

        $availableTypes = $lastAttendance ? ($validTransitions[$lastAttendance->type] ?? ['arrivee']) : ['arrivee'];

        // Vérifier si l'utilisateur peut prendre une pause (après 4h de travail)
        $canTakePause = false;
        if ($lastAttendance && in_array($lastAttendance->type, ['arrivee', 'reprise'])) {
            $lastArrival = Attendance::where('user_id', $request->user_id)
                ->where('site_id', $request->site_id)
                ->whereDate('timestamp', $currentTime->toDateString())
                ->whereIn('type', ['arrivee', 'reprise'])
                ->latest('timestamp')
                ->first();
                
            if ($lastArrival) {
                $workDuration = $currentTime->diffInMinutes($lastArrival->timestamp);
                if ($workDuration >= 240) {
                    $canTakePause = true;
                }
            }
        }

        // Vérifier si l'utilisateur peut partir (après l'heure de fin)
        $canDepart = true;
        $user = \App\Models\User::find($request->user_id);
        $site = \App\Models\Site::find($request->site_id);
        $dayOfWeek = strtolower($currentTime->format('l'));
        $endTime = $user->{'end_time_' . $dayOfWeek} ?? $site->{'end_time_' . $dayOfWeek};
        
        if ($endTime) {
            $endDateTime = Carbon::parse($endTime)->setDate(
                $currentTime->year,
                $currentTime->month,
                $currentTime->day
            );
            $canDepart = $currentTime->gte($endDateTime);
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'result' => [
                'is_on_pause' => $isOnPause,
                'pause_duration_minutes' => $pauseDuration,
                'pause_max_reached' => $pauseMaxReached,
                'last_attendance_type' => $lastAttendance ? $lastAttendance->type : null,
                'available_types' => $availableTypes,
                'can_take_pause' => $canTakePause,
                'can_depart' => $canDepart,
                'current_time' => $currentTime->format('H:i:s'),
                'expected_end_time' => $endTime
            ]
        ]);
    }
}