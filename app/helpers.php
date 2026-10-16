<?php

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\Auth;

if (!function_exists('user_type')) {
    function user_type()
    {
        return Auth::check() ? Auth::user()->user_type : null;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin()
    {
        return user_type() == 'super_admin' ? true : false;

    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return user_type() == 'admin' ? true : false;

    }
}

if (!function_exists('is_manager')) {
    function is_manager()
    {
        return user_type() == 'manager' ? true : false;

    }
}

if (!function_exists('is_controller')) {
    function is_controller()
    {
        return user_type() == 'controller' ? true : false;

    }
}

if (!function_exists('is_user')) {
    function is_user()
    {
        return user_type() == 'user' ? true : false;

    }
}

if (!function_exists('is_customer')) {
    function is_customer()
    {
        return user_type() == 'customer' ? true : false;

    }
}
