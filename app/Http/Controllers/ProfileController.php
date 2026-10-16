<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        Log::info('Profile edit page accessed', [
            'user_id' => Auth::id(),
            'user_type' => Auth::user()?->user_type
        ]);

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        Log::info('Profile update started', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['password', 'password_confirmation', 'current_password'])
        ]);

        try {
            $user = User::findOrFail(Auth::user()->id);
            Log::info('User found', ['user_id' => $user->id, 'email' => $user->email]);

            // Validation des données
            $validated = $request->validate([
                'name' => 'required|string',
                'first_name' => 'required|string',
                'email' => 'required|string|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'avatar' => 'nullable|file|mimetypes:image/*|max:2048',
                'emergency_contact_name' => 'nullable|string',
                'emergency_contact_email' => 'nullable|string|email',
                'emergency_contact_phone' => 'nullable|string',
                'emergency_contact_relationship' => 'nullable|string',
                'emergency_contact_address' => 'nullable|string',
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Mettre à jour tous les champs validés
            $user->fill($validated);
            Log::info('User filled with data');

            // Gérer l'avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                Log::info('Avatar file detected', [
                    'filename' => $request->file('avatar')->getClientOriginalName(),
                    'size' => $request->file('avatar')->getSize()
                ]);

                $path = $request->file('avatar')->store('avatars', 'public');
                Log::info('Avatar stored', ['path' => $path]);

                // Supprimer l'ancien avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                    Log::info('Old avatar deleted', ['old_path' => $user->avatar]);
                }

                $user->avatar = $path;
            }

            $user->save();
            Log::info('User saved successfully', ['user_id' => $user->id]);

            return redirect()
                ->route('profile.edit')
                ->with('success', 'Profil mis à jour avec succès.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in profile update', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur de validation: ' . json_encode($e->errors()));

        } catch (\Exception $e) {
            Log::error('Profile update error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du profil: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Display the user's company edit form.
     */
    public function editCompany(Request $request): View
    {
        Log::info('Company edit page accessed', [
            'user_id' => Auth::id(),
            'company_id' => Auth::user()?->company_id
        ]);

        $timezones = [];
        foreach (\DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = $timezone;
        }

        $company = Company::findOrFail(Auth::user()->company_id);

        return view('profile.edit_company', [
            'user' => $request->user(),
            'timezones' => $timezones,
            'company' => $company,
        ]);
    }

    /**
     * Update the company information.
     */
    public function updateCompany(Request $request)
    {
        Log::info('Company update started', [
            'user_id' => Auth::id(),
            'company_id' => Auth::user()?->company_id,
            'request_data' => $request->all()
        ]);

        try {
            $company = Company::findOrFail(Auth::user()->company_id);
            Log::info('Company found', ['company_id' => $company->id]);

            // Validation des données
            $validated = $request->validate([
                'compagny_name' => 'required|string|max:255',
                'compagny_email' => 'required|email|unique:companies,compagny_email,' . $company->id,
                'phone' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'timezone' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'badge_header_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
                'logo' => 'nullable|file|mimetypes:image/jpeg,image/png,image/jpg|max:2048',
            ]);

            Log::info('Company validation passed');

            // Mise à jour de l'utilisateur associé (admin)
            $user = User::where('email', $company->compagny_email)->first();
            if ($user) {
                $user->name = $request->compagny_name;
                $user->email = $request->compagny_email;
                $user->save();
                Log::info('Admin user updated', ['user_id' => $user->id]);
            }

            // Mise à jour de la compagnie
            $company->compagny_name = $request->compagny_name;
            $company->compagny_email = $request->compagny_email;
            $company->phone = $request->phone;
            $company->website = $request->website;
            $company->timezone = $request->timezone;
            $company->address = $request->address;
            $company->badge_header_color = $request->input('badge_header_color', '#1D4ED8');

            // Gestion du logo
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                Log::info('Logo file detected', [
                    'filename' => $request->file('logo')->getClientOriginalName(),
                    'size' => $request->file('logo')->getSize()
                ]);

                $logosDirectory = 'company_logos';

                // Créer le dossier s'il n'existe pas
                if (!Storage::disk('public')->exists($logosDirectory)) {
                    Storage::disk('public')->makeDirectory($logosDirectory);
                    Log::info('Logo directory created');
                }

                // Supprimer l'ancien logo s'il existe
                if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                    Storage::disk('public')->delete($company->logo);
                    Log::info('Old logo deleted', ['old_path' => $company->logo]);
                }

                // Générer un nom unique
                $filename = 'logo_' . time() . '_' . uniqid() . '.' . $request->file('logo')->getClientOriginalExtension();

                // Sauvegarder le nouveau logo
                $path = $request->file('logo')->storeAs($logosDirectory, $filename, 'public');
                $company->logo = $path;
                Log::info('New logo stored', ['path' => $path]);

                // Mettre à jour l'avatar de l'utilisateur admin avec le nouveau logo
                if ($user) {
                    // Supprimer l'ancien avatar
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                    $user->avatar = $path;
                    $user->save();
                    Log::info('Admin avatar updated');
                }
            }

            $company->save();
            Log::info('Company saved successfully', ['company_id' => $company->id]);

            return redirect()
                ->route('profile.edit.company')
                ->with('status', 'company-updated')
                ->with('success', __('Entreprise mise à jour avec succès.'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in company update', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur de validation: ' . json_encode($e->errors()));

        } catch (\Exception $e) {
            Log::error('Company update failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('Erreur lors de la mise à jour : ') . $e->getMessage());
        }
    }
}
