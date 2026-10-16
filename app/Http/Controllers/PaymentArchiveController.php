<?php
namespace App\Http\Controllers;

use App\Models\PaymentArchive;
use Illuminate\Http\Request;

class PaymentArchiveController extends Controller
{
    public function index(Request $request)
    {
        $archives = PaymentArchive::query()
            ->when($request->company_name, function($query, $name) {
                return $query->where('company_name', 'like', "%{$name}%");
            })
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->date_from, function($query, $date) {
                return $query->whereDate('payment_date', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                return $query->whereDate('payment_date', '<=', $date);
            })
            ->orderBy('archived_at', 'desc')
            ->paginate(20);

        // Statistiques
        $stats = [
            'total' => PaymentArchive::count(),
            'total_amount' => PaymentArchive::sum('amount'),
            'approved' => PaymentArchive::where('status', 'approved')->count(),
            'pending' => PaymentArchive::where('status', 'pending')->count(),
        ];

        return view('superadmin.payments-archive.index', compact('archives', 'stats'));
    }

    public function show(PaymentArchive $archive)
    {
        return view('superadmin.payments-archive.show', compact('archive'));
    }

    public function export(Request $request)
    {
        // Exporter en CSV ou Excel
        $archives = PaymentArchive::all();

        // Implémentation de l'export...
    }

public function destroy(PaymentArchive $archive)
{
    $archive->delete();

    return response()->json([
        'message' => 'Archive supprimée avec succès.'
    ]);
}

// Suppression groupée — un seul appel pour N lignes
public function bulkDestroy(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json(['message' => 'Aucun ID fourni.'], 422);
    }

    $deleted = PaymentArchive::whereIn('id', $ids)->delete();

    return response()->json([
        'message' => $deleted . ' archive(s) supprimée(s) avec succès.'
    ]);
}
}
