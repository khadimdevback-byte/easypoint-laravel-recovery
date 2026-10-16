<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PointageDelegateurController extends Controller
{
public function pointer(Request $request)
{
    $user = Auth::user() ?? User::find($request->user_id);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Utilisateur introuvable.'], 404);
    }

    if (!$user->type_horaire) {
        return response()->json(['success' => false, 'message' => 'Type d\'horaire non défini.'], 400);
    }

    try {
        if ($user->type_horaire === 'rotation') {
            // Appel direct du contrôleur existant
            $scanController = app(\App\Http\Controllers\Api\QrScanController::class);
            return $scanController->scan($request);
        }

        if ($user->type_horaire === 'fixe') {
            $attendanceController = app(\App\Http\Controllers\Api\AttendanceController::class);
            return $attendanceController->recordAttendance($request);
        }

        return response()->json(['success' => false, 'message' => 'Type d\'horaire non reconnu.'], 400);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()], 500);
    }
}

}
