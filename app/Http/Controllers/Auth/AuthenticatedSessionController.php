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
        if ($user->status != 'active') {

            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => __('Your account is not authorised to connect.'),
            ]);
        }

        // Régénérer la session après l'authentification
        $request->session()->regenerate();

        $dashboardRoute = match ($user->user_type) {
            'super_admin' => 'dashboard.superadmin',
            'admin' => 'dashboard',
            'manager' => 'manager.dashboard',
            'controller' => 'dashboard.controller',
            'user' => 'employee.dashboard',
            'customer' => 'customer.dashboard',
            default => null,
        };

        if ($dashboardRoute === null) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => __('Your account is not authorised to connect.'),
            ]);
        }

        return redirect()->route($dashboardRoute);
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
