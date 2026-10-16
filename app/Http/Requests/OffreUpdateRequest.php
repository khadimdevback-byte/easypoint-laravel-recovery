<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OffreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'site_id' => 'required|exists:sites,id',
            'poste_id' => 'required|exists:fonctions,id',
            'taux_horaire' => 'required|numeric',
            'nombre_personne_recherche' => 'required|integer|min:1',
            'description_offre' => 'nullable|string',
            'demarrage' => 'required|in:immediat,A_partir_de',
            'start_date' => ['nullable', 'required_if:demarrage,A_partir_de', 'date'],
        ];
    }
}
