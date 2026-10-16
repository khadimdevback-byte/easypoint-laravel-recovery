<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\FooterSetting;
use Illuminate\Http\Request;

class SiteVitrineController extends Controller
{
    public function acceuil()
    {
        $footer = FooterSetting::getSettings();
        return view('siteVitrine.acceuil', compact('footer'));
    }

    public function about()
    {
        $footer = FooterSetting::getSettings();
        return view('siteVitrine.apropos', compact('footer'));
    }

    public function contact()
    {
        $footer = FooterSetting::getSettings();
        return view('siteVitrine.contacte', compact('footer'));
    }

    public function fonctionnalites()
    {
        $footer = FooterSetting::getSettings();
        return view('siteVitrine.fonctionnalite', compact('footer'));
    }

    public function tarifs()
    {
        // Récupérer les plans actifs et visibles
        $plans = Plan::where('is_active', true)
            ->where('is_visible', true)
            ->get();

        $footer = FooterSetting::getSettings();

        return view('siteVitrine.tarifs', compact('plans', 'footer'));
    }

    // public function demonstration(Request $request)
    // {
    //     $timezones = $this->getFormattedTimezones();

    //     // Récupérer les plans actifs et visibles
    //     $plans = Plan::where('is_active', true)
    //         ->where('is_visible', true)
    //         ->orderBy('price', 'asc')
    //         ->get();

    //     $selectedPlanId = $request->query('plan');
    //     $billing = $request->query('billing', 'monthly');

    //     if (!$selectedPlanId && $plans->count() > 0) {
    //         $selectedPlanId = $plans->first()->id;
    //     }

    //     $footer = FooterSetting::getSettings();

    //     return view('siteVitrine.demonstration', compact('timezones', 'selectedPlanId', 'billing', 'plans', 'footer'));
    // }

    public function demonstration(Request $request)
    {
        $timezones = $this->getFormattedTimezones();

        $plans = Plan::where('is_active', true)
            ->where('is_visible', true)
            ->orderBy('price', 'asc')
            ->get();

        $selectedPlanId = $request->query('plan');
        $billing = $request->query('billing', 'monthly');

        if (!$selectedPlanId && $plans->count() > 0) {
            $selectedPlanId = $plans->first()->id;
        }

        $selectedPlan = null;
        if ($selectedPlanId) {
            $selectedPlan = Plan::find($selectedPlanId);
        }

        $planName = $selectedPlan ? $selectedPlan->name : 'Standard';
        $planPrice = $selectedPlan ? $selectedPlan->price : 0;
        $isFree = ($selectedPlan && $selectedPlan->price == 0) ? true : false;

        // ═══════════════════════════════════════════════
        // RÉCUPÉRER LES DONNÉES DE SESSION POUR LA NOTIFICATION
        // ═══════════════════════════════════════════════
        $registrationType = session('registration_type');
        $registrationPlanName = session('registration_plan_name');
        $registrationPlanPrice = session('registration_plan_price');
        $registrationEmail = session('registration_email');
        $registrationCompanyId = session('registration_company_id');

        $footer = FooterSetting::getSettings();

        return view('siteVitrine.demonstration', compact(
            'timezones',
            'selectedPlanId',
            'billing',
            'plans',
            'footer',
            'planName',
            'planPrice',
            'isFree',
            'registrationType',
            'registrationPlanName',
            'registrationPlanPrice',
            'registrationEmail',
            'registrationCompanyId'
        ));
    }

    private function getFormattedTimezones()
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $formattedTimezones = [];

        // Filtrer pour l'Afrique et l'Europe uniquement
        $filteredTimezones = array_filter($timezones, function($tz) {
            return str_starts_with($tz, 'Africa/') ||
                str_starts_with($tz, 'Europe/') ||
                $tz === 'UTC';
        });

        foreach ($filteredTimezones as $timezone) {
            try {
                $dateTime = new \DateTime('now', new \DateTimeZone($timezone));
                $offset = $dateTime->format('P');
                $display = str_replace('_', ' ', $timezone) . ' (UTC' . $offset . ')';
                $formattedTimezones[$timezone] = $display;
            } catch (\Exception $e) {
                $formattedTimezones[$timezone] = str_replace('_', ' ', $timezone);
            }
        }

        // Trier par continent puis par nom
        ksort($formattedTimezones);

        return $formattedTimezones;
    }

    /**
 * Affiche la page de démonstration avec le résultat de l'inscription
 */
    public function demonstrationResult(Request $request)
    {
        // Vérifier si les paramètres sont présents
        if (!$request->has('status') || $request->status !== 'success') {
            return redirect()->route('siteVitrine.demonstration');
        }

        // Récupérer les paramètres
        $type = $request->get('type', 'free');
        $planName = $request->get('plan_name', 'Standard');
        $planPrice = $request->get('plan_price', 0);
        $companyId = $request->get('company_id');
        $email = $request->get('email');

        // Récupérer les données pour le formulaire
        $timezones = $this->getFormattedTimezones();

        $plans = Plan::where('is_active', true)
            ->where('is_visible', true)
            ->orderBy('price', 'asc')
            ->get();

        $selectedPlanId = $request->query('plan');
        $billing = $request->query('billing', 'monthly');

        if (!$selectedPlanId && $plans->count() > 0) {
            $selectedPlanId = $plans->first()->id;
        }

        // Récupérer les infos du plan sélectionné
        $selectedPlan = null;
        if ($selectedPlanId) {
            $selectedPlan = Plan::find($selectedPlanId);
        }

        $planNameDisplay = $selectedPlan ? $selectedPlan->name : 'Standard';
        $planPriceDisplay = $selectedPlan ? $selectedPlan->price : 0;
        $isFree = ($selectedPlan && $selectedPlan->price == 0) ? true : false;

        $footer = FooterSetting::getSettings();

        // Passer les variables à la vue
        return view('siteVitrine.demonstration', compact(
            'timezones',
            'selectedPlanId',
            'billing',
            'plans',
            'footer',
            'planNameDisplay',
            'planPriceDisplay',
            'isFree',
            'type',
            'planName',
            'planPrice',
            'companyId',
            'email'
        ));
    }
}
