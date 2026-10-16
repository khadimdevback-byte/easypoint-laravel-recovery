<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * Affiche le formulaire de contact
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        $messages = SupportMessage::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('company.support.index', compact('messages'));
    }

    /**
     * Formulaire pour envoyer un message
     */
    public function create()
    {
        return view('company.support.create');
    }

    /**
     * Envoie un message au support
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:question,bug,facturation,renouvellement,autre',  // ← ici : bug au lieu de probleme
            'message' => 'required|string|min:10',
        ]);

        $user = Auth::user();
        $company = $user->company;

        $support = SupportMessage::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'subject' => $request->subject,
            'category' => $request->category,
            'message' => $request->message,
            'status' => 'en_attente',
        ]);

        // Envoyer un email au support (à configurer)
        // Mail::to('support@easypoint.com')->send(new NewSupportMessage($support));

        return redirect()->route('support.index')
            ->with('success', 'Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }
    /**
     * Affiche un message spécifique
     */
    public function show(SupportMessage $support)
    {
        $user = Auth::user();

        if ($support->company_id !== $user->company->id) {
            abort(403);
        }

        return view('company.support.show', compact('support'));
    }

    /**
 * Répondre à un message (entreprise)
 */
    public function reply(Request $request, SupportMessage $support)
    {
        $user = Auth::user();

        if (!$user->company || $support->company_id !== $user->company->id) {
            abort(403);
        }

        $request->validate([
            'reply_message' => 'required|string|min:3'
        ]);

        // IMPORTANT : parent_id doit être l'ID du message original
        $parentId = $support->parent_id ?? $support->id;

        $reply = SupportMessage::create([
            'company_id' => $user->company->id,
            'user_id' => $user->id,
            'subject' => 'Re: ' . $support->subject,
            'category' => $support->category,
            'message' => $request->reply_message,
            'status' => 'en_cours',
            'parent_id' => $parentId,  // ← C'est CRUCIAL !
            'sender_type' => 'company',
        ]);

        // Mettre à jour le statut du message original
        $support->update(['status' => 'en_cours']);

        return redirect()->route('support.show', $support)
            ->with('success', 'Votre réponse a été envoyée.');
    }

    /**
 * Supprimer un message (soft delete)
 */
    public function destroy(SupportMessage $support)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur appartient bien à la compagnie du message
        if (!$user->company || $support->company_id !== $user->company->id) {
            abort(403, 'Accès non autorisé.');
        }

        // Vérifier que le message n'a pas de réponses
        if ($support->replies()->count() > 0) {
            return redirect()->route('support.index')
                ->with('error', 'Vous ne pouvez pas supprimer un message qui a des réponses.');
        }

        // Soft delete du message
        $support->delete();

        return redirect()->route('support.index')
            ->with('success', 'Message supprimé avec succès.');
    }
}
