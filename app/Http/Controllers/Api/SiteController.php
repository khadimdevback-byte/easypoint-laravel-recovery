<?php

namespace App\Http\Controllers\Api;

use App\Models\Site;
use App\Models\User;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{



    public function site(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user) {
            $siteIds = SiteUser::where('user_id', $request->user_id)
                ->pluck('site_id');

            $site = Site::with(['users'])
                ->where('company_id', $user->company_id)
                ->where('status', 'active')
                ->whereIn('id', $siteIds)->first();

          
                $return = [
                    'status' => 200,
                    'message' => __('success'),
                    'result' => [
                        'site' => $site ??[],
                    ],
                ];
            
                return response()->json($return, $return['status']);
          
       
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }
}
