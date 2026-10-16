<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanySubscription
{
    /**
     * Handle an incoming request.
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $user = Auth::user();

    //     if ($user && $user->company) {
    //         if ($user->user_type === 'admin') {
    //             $subscription = $user->company->subscription;
    //             if (!$subscription || !$subscription->plan_id || $subscription->status !== 'active') {
    //                 if ($request->routeIs('subscription.payment') ||
    //                     $request->routeIs('registration.payment.callback') ||
    //                     $request->routeIs('logout')) {
    //                     return $next($request);
    //                 }
    //                 return redirect()->route('subscription.payment', ['company' => $user->company->id])
    //                     ->with('error', 'Votre société doit souscrire à un plan pour activer la plateforme.');
    //             }

    //         } else {
    //             $subscription = $user->company->subscription;
    //             if (!$subscription || !$subscription->plan_id || $subscription->status !== 'active') {
    //                 Auth::logout();
    //                 return redirect()->route('login')
    //                     ->with('error', 'L\'accès à votre espace est temporairement suspendu. Veuillez contacter l\'administrateur de votre entreprise.');
    //             }
    //         }
    //     }

    //     return $next($request);
    // }

public function handle(Request $request, Closure $next): Response
{
    $user = Auth::user();

    if ($user && $user->company) {
        // 🔥 CRUCIAL : Laisser passer si déjà sur la page de paiement ou login
        if ($request->routeIs('subscription.payment') ||
            $request->routeIs('registration.payment.callback') ||
            $request->routeIs('login') ||
            $request->routeIs('logout')) {
            return $next($request);
        }

        if ($user->user_type === 'admin') {
            // Chercher un abonnement ACTIF uniquement
            $activeSubscription = $user->company->subscription()
                ->where('status', 'active')
                ->first();

            // Chercher un abonnement en attente
            $pendingSubscription = $user->company->subscription()
                ->where('status', 'pending_payment')
                ->first();

            // Si pas d'actif mais un en attente → page de paiement
            if (!$activeSubscription && $pendingSubscription) {
                return redirect()->route('subscription.payment', ['company' => $user->company->id])
                    ->with('info', 'Veuillez finaliser votre paiement.');
            }

            // Si ni actif ni en attente → error
            if (!$activeSubscription) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Votre abonnement n\'est pas actif. Veuillez contacter l\'administrateur.');
            }
        }
    }

    return $next($request);
}
}
