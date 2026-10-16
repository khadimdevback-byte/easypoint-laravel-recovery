<?php
// app/Http/Controllers/FooterController.php

namespace App\Http\Controllers;

use App\Models\FooterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FooterController extends Controller
{
    /**
     * Afficher les paramètres du footer
     */
    public function index()
    {
        $footer = FooterSetting::getSettings();
        return view('superadmin.footer.index', compact('footer'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit()
    {
        $footer = FooterSetting::getSettings();
        return view('superadmin.footer.edit', compact('footer'));
    }

    /**
     * Mettre à jour les paramètres du footer
     */
    public function update(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:200',
            'copyright_text' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Récupérer ou créer l'enregistrement
        $footer = FooterSetting::first();

        if ($footer) {
            $footer->update($request->only([
                'email',
                'phone',
                'address',
                'copyright_text'
            ]));
        } else {
            FooterSetting::create($request->only([
                'email',
                'phone',
                'address',
                'copyright_text'
            ]));
        }

        return redirect()->route('superadmin.footer.index')
            ->with('success', 'Footer mis à jour avec succès !');
    }

    /**
     * Réinitialiser les paramètres du footer
     */
    public function reset()
    {
        FooterSetting::truncate();
        FooterSetting::createDefault();

        return redirect()->route('superadmin.footer.index')
            ->with('success', 'Footer réinitialisé avec succès !');
    }
}
