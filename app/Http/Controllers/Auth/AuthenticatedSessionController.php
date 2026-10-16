<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authentifier l'utilisateur
        $request->authenticate();
        // Récupérer l'utilisateur authentifié
        $user = $request->user();
        // Vérifier le type d'utilisateur
        if (in_array($user->user_type, ['customer'])) {
            // Déconnecter l'utilisateur si son type est 'customer'
            Auth::logout();

            // Rediriger l'utilisateur avec un message d'erreur
            return redirect()->route('login')->withErrors([
                'email' => __('Your account is not authorised to connect.'),
            ]);
        }

        if ($user->status != 'active') {

            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => __('Your account is not authorised to connect.'),
            ]);
        }

        // Régénérer la session après l'authentification
        $request->session()->regenerate();

        if (Auth::user()->user_type == 'super_admin') {

            return redirect()->intended(route('dashboard.superadmin', absolute: false));
        } elseif (Auth::user()->user_type == 'controller') {

            return redirect()->intended(route('dashboard.controller', absolute: false));
        }elseif (Auth::user()->user_type == 'user') {

            return redirect()->intended(route('employee.dashboard', absolute: false));
        } elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'manager') {

            return redirect()->intended(route('dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('login');
    }
}
