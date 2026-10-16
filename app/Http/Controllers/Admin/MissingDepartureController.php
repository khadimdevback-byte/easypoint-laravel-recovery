<?php
// app/Http/Controllers/Admin/MissingDepartureController.php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\MissingDeparture;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MissingDepartureController extends Controller
{
    public function index()
    {
        // Récupérer l'admin connecté
        $admin = Auth::user();

        // Récupérer les demandes en attente des employés de la même compagnie
        $pendingRequests = MissingDeparture::with(['user', 'site', 'company'])
            ->where('status', 'pending')
            ->where('company_id', $admin->company_id)  // Filtrer par compagnie
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Récupérer les demandes approuvées des employés de la même compagnie
        $approvedRequests = MissingDeparture::with(['user', 'site', 'company', 'approver'])
            ->where('status', 'approved')
            ->where('company_id', $admin->company_id)  // Filtrer par compagnie
            ->orderBy('approved_at', 'desc')
            ->limit(20)
            ->get();

        // Statistiques pour la compagnie
        $stats = [
            'total_pending' => MissingDeparture::where('company_id', $admin->company_id)
                ->where('status', 'pending')
                ->count(),
            'total_approved' => MissingDeparture::where('company_id', $admin->company_id)
                ->where('status', 'approved')
                ->count(),
            'total_rejected' => MissingDeparture::where('company_id', $admin->company_id)
                ->where('status', 'rejected')
                ->count(),
        ];

        return view('admin.missing-departures', compact('pendingRequests', 'approvedRequests', 'stats'));
    }

    public function approve(Request $request, $id)
    {
        $admin = Auth::user();
        $missingDeparture = MissingDeparture::findOrFail($id);

        // Vérifier que la demande appartient à la compagnie de l'admin
        if ($missingDeparture->company_id !== $admin->company_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à valider cette demande.');
        }

        // Créer le pointage de départ manquant
        $departureTime = $missingDeparture->expected_depart_time
            ? Carbon::parse($missingDeparture->date)->setTimeFromTimeString($missingDeparture->expected_depart_time)
            : Carbon::parse($missingDeparture->date)->setTime(17, 0, 0);

        Attendance::create([
            'user_id' => $missingDeparture->user_id,
            'site_id' => $missingDeparture->site_id,
            'company_id' => $missingDeparture->company_id,  // Utiliser company_id de la demande
            'timestamp' => $departureTime,
            'type' => 'depart',
            'source' => 'admin_validation',
            'status' => 'present',
            'day_status' => 'validated',
            'admin_note' => 'Départ validé par admin - Pointage manquant'
        ]);

        // Mettre à jour la demande
        $missingDeparture->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'admin_comment' => $request->input('comment') ?? 'Validé par administrateur'
        ]);

        return redirect()->back()->with('success', 'Départ validé et pointage ajouté avec succès.');
    }

    public function reject(Request $request, $id)
    {
        try {
            $admin = Auth::user();
            $missingDeparture = MissingDeparture::findOrFail($id);

            // Vérifier que la demande appartient à la compagnie de l'admin
            if ($missingDeparture->company_id !== $admin->company_id) {
                return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à rejeter cette demande.');
            }

            $comment = $request->input('comment');
            if (empty($comment)) {
                return redirect()->back()->with('error', 'Veuillez indiquer un motif de rejet.');
            }

            // 1. Mettre à jour la demande
            $missingDeparture->update([
                'status' => 'rejected',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'admin_comment' => $comment
            ]);

            // 2. Récupérer et supprimer l'arrivée du jour
            $arrivalOfDay = Attendance::where('user_id', $missingDeparture->user_id)
                ->where('site_id', $missingDeparture->site_id)
                ->whereDate('timestamp', $missingDeparture->date)
                ->where('type', 'arrivee')
                ->first();

            if ($arrivalOfDay) {
                $arrivalOfDay->delete();
            }

            return redirect()->back()->with('success', 'Demande rejetée. L\'arrivée a été annulée et la journée est comptée comme absence.');

        } catch (\Exception $e) {
            Log::error('Erreur lors du rejet de la demande', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue lors du rejet de la demande.');
        }
    }
}
