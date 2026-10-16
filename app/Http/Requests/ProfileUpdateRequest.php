<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // 'n_social' => ['required', 'string'],
            'name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user()->id)],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'salary_rate' => ['nullable', 'string'],
            'avatar' => ['nullable', 'file', 'mimetypes:image/*', 'max:2048'],
            'emergency_contact_name' => ['nullable', 'string'],
            'emergency_contact_email' => ['nullable', 'email'],
            'emergency_contact_phone' => ['nullable', 'string'],
            'emergency_contact_relationship' => ['nullable', 'string'],
            'emergency_contact_address' => ['nullable', 'string'],
        ];

        // Pour les super admins, ajouter la validation des champs supplémentaires
        if (auth()->user() && auth()->user()->user_type === 'admin') {
            $rules['user_type'] = ['required', Rule::in(['manager', 'controller', 'user', 'customer'])];
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // 'n_social.required' => 'Le numéro de sécurité sociale est requis',
            'email.required' => 'L\'email est requis',
            'email.email' => 'Veuillez entrer un email valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'avatar.max' => 'L\'avatar ne doit pas dépasser 2MB',
            'user_type.required' => 'Le type d\'utilisateur est requis',
            'company_id.required' => 'La société est requise',
            'company_id.exists' => 'La société sélectionnée n\'existe pas',
        ];
    }
}
