<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SendCreateUserEmailNotification;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('super_admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            // 'n_social' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'avatar' => 'nullable|file|mimetypes:image/*|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $user = new User([
            'user_type' => 'super_admin',
            'name' => $request->name,
            'first_name' => $request->first_name,
            // 'n_social' => $request->n_social,
            'email' => $request->email,
            'status' => $request->status,
        ]);


        $userData = [
            'email' => $request->email,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'name' => $request->name,
            'phone' => $request->phone,
            // 'n_social' => $request->n_social,
            'password' => $request->password,
        ];
        $user->password = Hash::make($request->password);



        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {

            $avatarsDirectory = 'avatars';
            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }
            $filename = 'avatar_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();

            $user->avatar = $request->file('avatar')->storeAs($avatarsDirectory, $filename, 'public');
        }


        try {
            $user->save();
        } catch (\Exception $e) {
            Log::error('Failed to create super admin.', ['exception' => $e]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failure to create super admin'));
        }

        try {
            $user->notify(new SendCreateUserEmailNotification($userData));
        } catch (\Exception $e) {
            Log::warning('Super admin was created, but the welcome email could not be sent.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
            return redirect()
                ->route('users.index', 'super_admin')
                ->with('success', __('Super administrator successfully created. The welcome email could not be sent.'));
        }

        return redirect()
            ->route('users.index', 'super_admin')
            ->with('success', __('Super administrator successfully created.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $user_type, string $id)
    {
        $user = User::findOrFail($id);
        return view('super_admin.edit', compact('user', 'user_type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $user_type, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            // 'n_social' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|file|mimetypes:image/*|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->first_name = $request->first_name;
        // $user->n_social = $request->n_social;
        $user->email = $request->email;
        $user->status = $request->status;

        if (!empty(trim($request->password))) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarsDirectory = 'avatars';

            if (!Storage::disk('public')->exists($avatarsDirectory)) {
                Storage::disk('public')->makeDirectory($avatarsDirectory);
            }

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $filename = 'avatar_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $user->avatar = $request->file('avatar')->storeAs($avatarsDirectory, $filename, 'public');
        }

        try {
            $user->save();

            // Utilisation dynamique du $user_type pour la redirection
            return redirect()
                ->route('users.index', $user_type)
                ->with('success', __('Super Admin has been successfully updated.'));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Failed to update super admin'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if ($id == 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('The primary super admin cannot be deleted.'));
        } else {
            $user = User::findOrFail($id);
            // Supprimer l'image d'avatar si elle existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();
            return redirect()->route('users.index',$user->user_type)->with('success', 'User deleted successfully.');
        }
    }
}
