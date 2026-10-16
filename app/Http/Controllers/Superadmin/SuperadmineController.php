<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SuperadmineController extends Controller
{
    /**
     * Liste tous les messages de support
     */
    public function index()
    {
        $messages = SupportMessage::with('company')
            ->whereNull('parent_id')  // Afficher seulement les messages principaux
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('superadmin.support.index', compact('messages'));
    }

    /**
     * Affiche un message spécifique
     */
    public function show(SupportMessage $support)
    {
        $support->load('company', 'user', 'replies');
        return view('superadmin.support.show', compact('support'));
    }

    /**
     * Répondre à un message
     */

    public function reply(Request $request, SupportMessage $support)
    {
        $request->validate([
            'response' => 'required|string|min:5'
        ]);

        // IMPORTANT : parent_id doit être l'ID du message original (s'il n'a pas de parent)
        // ou l'ID du parent (s'il a déjà un parent)
        $parentId = $support->parent_id ?? $support->id;

        $reply = SupportMessage::create([
            'company_id' => $support->company_id,
            'user_id' => null,
            'subject' => 'Re: ' . $support->subject,
            'category' => $support->category,
            'message' => $request->response,
            'status' => 'en_cours',
            'parent_id' => $parentId,  // ← C'est CRUCIAL !
            'sender_type' => 'superadmin',
        ]);

        // Mettre à jour le statut du message original
        $support->update(['status' => 'en_cours']);

        return redirect()->route('superadmin.support.show', $support)
            ->with('success', 'Réponse envoyée avec succès.');
    }


    /**
     * Changer le statut du message
     */
    public function status(Request $request, SupportMessage $support)
    {
        $request->validate([
            'status' => 'required|in:en_attente,en_cours,resolu,ferme'
        ]);

        $support->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(SupportMessage $support)
    {
        if ($support->replies()->count() > 0) {
            return redirect()->route('superadmin.support.index')
                ->with('error', 'Vous ne pouvez pas supprimer un message qui a des réponses.');
        }
        $support->delete();
        return redirect()->route('superadmin.support.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}
