<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordCode;

class AuthController extends Controller
{
public function login(Request $request)
{
    $request->validate([
        'identifier' => 'required|string',
        'password' => 'required|string|min:8',
    ]);

    $user = User::where('username', $request->identifier)
        ->orWhere('email', $request->identifier)
        ->where('user_type', 'user')
        ->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur introuvable.',
        ], 404);
    }

    if (!Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Mot de passe incorrect.',
        ], 401);
    }

    Log::debug("Tentative de login de {$user->email} avec status {$user->status}");

    Auth::login($user);

    Log::debug("Login réussi !");
 // connecte manuellement
    $token = $user->createToken('API Token')->plainTextToken;
    // Enregistrer OneSignal ID
    $user->one_signal_user_id = $request->one_signal_user_id;
    $user->save();

    if ($user->avatar) {
        $user->avatar = url('storage/' . $user->avatar);
    } else {
        $user->avatar = asset('images/favicon.png');
    }

    $user->employee_function = $user->fonction ? $user->fonction->nom_fonction : "";

    return response()->json([
        'success' => true,
        'user' => $user,
        'token' => $token,
    ]);
}



    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'identifier' => 'required|string',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     // Vérifiez si l'utilisateur existe par son email ou son nom d'utilisateur
    //     $user = User::where('username', $request->identifier)
    //         ->orWhere('email', $request->identifier)
    //         ->where('user_type', 'user')
    //         ->first();

    //     // Si l'utilisateur existe et que le mot de passe est correct
    //     if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->password])) {

    //         // Vérifier s'il y a déjà un token actif
    //         $existingToken = $user->tokens()->where('revoked', false)->first();
    //         // Si un token actif existe, renvoyer un message d'erreur
    //         if ($existingToken) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Vous êtes déjà connecté sur un autre appareil. Veuillez vous déconnecter d\'abord.',
    //             ], 403); // Code 403 pour "Forbidden"
    //         }

    //         // Créer un nouveau token si aucun token actif n'est trouvé
    //         $token = $user->createToken('API Token')->plainTextToken;

    //         if ($user && $user->avatar) {
    //             $user->avatar = $user->avatar
    //                 ? url('storage/' . $user->avatar)
    //                 : asset('images/favicon.png');
    //         }

    //         // Remplacer l'ID par la valeur de la fonction
    //         $user->employee_function = $user->fonction ? $user->fonction->name : "";

    //         return response()->json([
    //             'success' => true,
    //             'user' => $user,
    //             'token' => $token,
    //         ], 200);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Identifiants invalides.',
    //     ], 401);
    // }


    public function sendResetCode(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé.',
            ], 404);
        }

        try {
            // Générer un code à 6 chiffres
            $code = random_int(100000, 999999);

            // Stocker le code et son expiration
            $user->reset_code = $code;
            $user->reset_code_expires_at = Carbon::now()->addMinutes(10);
            $user->save();

            // Envoyer le code par email
            $user->notify(new ResetPasswordCode($code));

            return response()->json([
                'success' => true,
                'message' => 'Un code de réinitialisation a été envoyé à votre adresse email.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du code : ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi de l\'email. Veuillez réessayer.',
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {


        $request->validate([
            'identifier' => 'required|string',
            'reset_code' => 'required|string|size:6',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->identifier)
            ->orWhere('username', $request->identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé.',
            ], 404);
        }



        // Vérifier si le code est valide et non expiré
        if (
            $user->reset_code !== $request->reset_code ||
            $user->reset_code_expires_at < Carbon::now()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Code de réinitialisation invalide ou expiré.',
            ], 401);
        }

        try {
            // Réinitialiser le mot de passe
            $user->password = bcrypt($request->password);
            $user->reset_code = null;
            $user->reset_code_expires_at = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Votre mot de passe a été réinitialisé avec succès.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la réinitialisation du mot de passe : ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }
}
