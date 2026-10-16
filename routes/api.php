<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\OffreController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CandidatureController;
use App\Http\Controllers\Api\DisponibiliteController;
use App\Http\Controllers\Api\GetAttendanceController;
use App\Http\Controllers\Api\AttendanceWorkflowController;
use App\Http\Controllers\Api\BilanPointageController;
use App\Http\Controllers\Api\PointageDelegateurController;
use App\Http\Controllers\Api\QrScanController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\SubscriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('password/forgot', [AuthController::class, 'sendResetCode']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/fedapay/webhook', [SubscriptionController::class, 'handleWebhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/record', [AttendanceController::class, 'recordAttendance']);
    Route::any('/addOrUpdatelastAttendanceType', [AttendanceWorkflowController::class, 'addOrUpdatelastAttendanceType']);
    Route::get('/get_last_attendance_type', [AttendanceWorkflowController::class, 'getLastAttendanceType']);
    Route::post('/get_monthly_attendance_data', [GetAttendanceController::class, 'getMonthlyAttendanceData']);
    Route::post('/get_last_seven_days_status', [GetAttendanceController::class, 'getLastSevenDaysStatus']);
    Route::post('/site', [SiteController::class, 'site']);
    Route::post('/change_password', [AuthController::class, 'changePassword']);

    Route::get('offresListe', [OffreController::class, 'index']);
    Route::get('/offresDetail/{id}', [OffreController::class, 'show']);
    Route::post('offre/{id}/postuler', [CandidatureController::class, 'postuler']);
    Route::post('candidature/{id}/decliner', [CandidatureController::class, 'decliner'])->name('candidature.decliner');
    Route::get('/employment-status', [CandidatureController::class, 'checkEmploymentStatus']);
    Route::get('/mescandidatures', [CandidatureController::class, 'Candidatures']);

    Route::post('/disponibilitesAjouter', [DisponibiliteController::class, 'store']); // Ajouter
    Route::get('/mes-disponibilites', [DisponibiliteController::class, 'mesDisponibilites']);
    Route::put('/disponibilitesModifier/{id}', [DisponibiliteController::class, 'update']);
    Route::delete('disponibilitesSupprimer/{id}', [DisponibiliteController::class, 'destroy']);

    Route::get('/checkContrat', [DisponibiliteController::class, 'checkUserContract']);
    Route::get('/checkdisponibilite',[DisponibiliteController::class,'checkdisponibilite']);
    Route::post('/scan-pointage', [QrScanController::class, 'scan']);
    Route::post('/pointage', [PointageDelegateurController::class, 'pointer']);
    Route::get('/bilan-rotation', [BilanPointageController::class, 'bilanRotation']);

    Route::get('/employe/demandes', [DemandeController::class, 'listeEmploye']);
    Route::post('/employe/demandes', [DemandeController::class, 'enregistrerParEmploye']);
    Route::get('/employe/demandes', [DemandeController::class, 'listeEmploye']);
    Route::get('/employe/demandes/{id}', [DemandeController::class, 'detailEmploye']);
    Route::post('/employe/demandes', [DemandeController::class, 'enregistrerParEmploye']);
    Route::put('/employe/demandes/{id}', [DemandeController::class, 'modifierEmploye']);
    Route::delete('/employe/demandes/{id}', [DemandeController::class, 'supprimerEmploye']);
    Route::patch('/employe/demandes/{id}/annuler', [DemandeController::class, 'annulerEmploye']);

    Route::get('/listepointage', [AttendanceController::class, 'getUserPointages']);
    Route::get('/listepointage/{userId}/{year}/{month}/{day}', [AttendanceController::class, 'getPointagesByDay']);

    // Route::get('/verify-site/{qr_code}', function($qr_code) {
    //     $site = \App\Models\SiteRotation::with('company')
    //         ->where('qr_code', operator: $qr_code)
    //         ->first();

    //     if (!$site) {
    //         return response()->json(['error' => 'QR code non enregistré'], 404);
    //     }

    //     return response()->json([
    //         'site' => $site->only('id', 'nom', 'qr_code', 'latitude', 'longitude', 'radius'),
    //         'company' => $site->company->name,
    //         'is_valid' => $site->latitude && $site->longitude && $site->radius
    //     ]);
    // });
});















