<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\ContactNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Afficher la page de contact
     */
    public function index()
    {
        return view('siteVitrine.contacte');
    }

    /**
     * Traiter le formulaire de contact
     */
    public function submit(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'required|string|max:255',
            'employees' => 'nullable|string|max:100',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'intent' => 'required|string|in:general,devis,support',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,doc,docx,png,jpg,jpeg',
            'rgpd' => 'accepted'
        ], [
            'first_name.required' => 'Le prénom est requis',
            'last_name.required' => 'Le nom est requis',
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
            'company.required' => 'Le nom de l\'entreprise est requis',
            'subject.required' => 'Le sujet est requis',
            'message.required' => 'Le message est requis',
            'message.min' => 'Le message doit contenir au moins 10 caractères',
            'attachment.max' => 'Le fichier ne doit pas dépasser 5 Mo',
            'attachment.mimes' => 'Le fichier doit être au format PDF, DOC, DOCX, PNG ou JPG',
            'rgpd.accepted' => 'Vous devez accepter la politique de confidentialité'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Gestion du fichier
        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('contacts', $attachmentName, 'public');
        }

        // Sauvegarde en base de données
        $contact = Contact::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'employees' => $request->employees,
            'subject' => $request->subject,
            'message' => $request->message,
            'intent' => $request->intent,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'pending'
        ]);

        // Créer une notification pour les admins
        $this->createNotification($contact);

        // Envoi d'email
        try {
            // Email à l'admin
            Mail::to(config('mail.contact_email', 'contact@easypoint.com'))->send(new ContactMail($contact));
            // Copie à l'utilisateur
            Mail::to($request->email)->send(new ContactMail($contact, true));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email contact: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
            'data' => $contact
        ]);
    }

    /**
     * Créer une notification pour les administrateurs
     */
    private function createNotification($contact)
    {
        // Messages personnalisés selon l'intention
        $messages = [
            'general' => '📬 Nouveau message de contact de ' . $contact->first_name . ' ' . $contact->last_name,
            'devis' => '💰 Nouvelle demande de devis de ' . $contact->company,
            'support' => '🔧 Nouvelle demande de support technique de ' . $contact->first_name,
        ];

        $message = $messages[$contact->intent] ?? '📬 Nouveau message de ' . $contact->first_name . ' ' . $contact->last_name;

        // Créer la notification
        $notification = ContactNotification::create([
            'contact_id' => $contact->id,
            'user_id' => null,
            'type' => 'new_contact',
            'message' => $message,
            'data' => [
                'contact_id' => $contact->id,
                'name' => $contact->first_name . ' ' . $contact->last_name,
                'email' => $contact->email,
                'company' => $contact->company,
                'intent' => $contact->intent,
                'subject' => $contact->subject,
                'created_at' => $contact->created_at->format('Y-m-d H:i:s')
            ],
            'is_read' => false
        ]);

        // Déclencher un événement broadcast (pour WebSockets si configuré)
        try {
            broadcast(new \App\Events\NewContactNotification($notification))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Broadcast non disponible: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Récupérer les notifications non lues (API pour le frontend)
     */
    public function getUnreadNotifications()
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notifications = ContactNotification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $count = $notifications->count();

        return response()->json([
            'success' => true,
            'count' => $count,
            'notifications' => $notifications
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead($id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notification = ContactNotification::findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
            'user_id' => auth()->id()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllNotificationsAsRead()
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        ContactNotification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
            'user_id' => auth()->id()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Admin - Liste des messages
     */
    public function adminIndex(Request $request)
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            abort(401, 'Non authentifié');
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $filters = $request->validate([
            'status' => 'nullable|in:pending,processing,responded,closed',
            'sort' => 'nullable|in:asc,desc',
        ]);

        $contacts = Contact::query()
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy('created_at', $filters['sort'] ?? 'desc')
            ->paginate(20)
            ->withQueryString();
        $statusCounts = Contact::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $unreadCount = ContactNotification::where('is_read', false)->count();

        return view('admin.contacts.index', compact('contacts', 'unreadCount', 'statusCounts'));
    }

    /**
     * Admin - Voir un message
     */
    public function adminShow($id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            abort(401, 'Non authentifié');
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            abort(403, 'Accès non autorisé');
        }

        $contact = Contact::findOrFail($id);

        if (!$contact->is_read) {
            $contact->update(['is_read' => true, 'read_at' => now()]);
        }

        // Marquer les notifications liées comme lues
        ContactNotification::where('contact_id', $id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'user_id' => auth()->id()
            ]);

        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Admin - Changer le statut
     */
    public function adminStatus(Request $request, $id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $contact = Contact::findOrFail($id);
        $oldStatus = $contact->status;

        $contact->update([
            'status' => $request->status,
            'responded_at' => now(),
            'responded_by' => auth()->id()
        ]);

        // Créer une notification pour le changement de statut
        ContactNotification::create([
            'contact_id' => $contact->id,
            'user_id' => auth()->id(),
            'type' => 'status_changed',
            'message' => '✅ Statut du message #' . $contact->id . ' changé : ' . $oldStatus . ' → ' . $request->status,
            'data' => [
                'contact_id' => $contact->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ],
            'is_read' => false
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Admin - Supprimer un message
     */
    public function adminDestroy($id)
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $contact = Contact::findOrFail($id);

        // Supprimer les notifications liées
        ContactNotification::where('contact_id', $id)->delete();

        // Supprimer le fichier s'il existe
        if ($contact->attachment && Storage::disk('public')->exists($contact->attachment)) {
            Storage::disk('public')->delete($contact->attachment);
        }

        $contact->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Dashboard - Widget des dernières notifications
     */
    public function getLatestNotifications()
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        // Vérifier que l'utilisateur est admin ou super_admin
        $userType = auth()->user()->user_type;
        if (!in_array($userType, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $notifications = ContactNotification::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = ContactNotification::where('is_read', false)->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }
}
