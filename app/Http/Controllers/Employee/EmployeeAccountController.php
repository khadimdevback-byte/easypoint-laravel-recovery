<?php

namespace App\Http\Controllers\Employee;

use App\Models\Site;
use App\Models\User;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDeactivationReason;

class EmployeeAccountController extends Controller
{
    public function index(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function password(Request $request)
    {
        $user = $request->user()->load('fonction');
        // Initialisation de $sites pour éviter l'erreur "Undefined variable"
        $sites = collect(); // Collection vide

        $siteIds = SiteUser::where('user_id', $user->id)
            ->pluck('site_id');

        $sites = Site::with(['company', 'customer', 'controller', 'users'])
            ->where('company_id', Auth::user()->company_id)
            ->whereIn('id', $siteIds)
            ->paginate(25);


        // Passer les données à la vue
        return view('employee.password', [
            'user' => $user,
            'sites' => $sites
        ]);
    }
}
