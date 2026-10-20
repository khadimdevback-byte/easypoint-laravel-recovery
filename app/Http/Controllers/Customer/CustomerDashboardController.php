<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user();
        abort_unless($customer?->user_type === 'customer', 403);

        return view('customer.dashboard', [
            'customer' => $customer,
            'company' => $customer->company,
        ]);
    }
}
