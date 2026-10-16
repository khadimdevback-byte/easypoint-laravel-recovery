<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OffreStoreRequest extends FormRequest
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
            //
            'type_horaire' => 'required|in:fixe,rotation',
            'site_id' => 'required_if:type_horaire,fixe|nullable|exists:sites,id',
            'site_rotation_id' => 'required_if:type_horaire,rotation|nullable|exists:site_rotation,id',
            'poste_id'=>'required',
            'taux_horaire'=>'nullable',
            'nombre_personne_recherche'=>'required',
            'demarrage' => 'required|in:immediat,A_partir_de',
            'start_date' => [
                'nullable', // Peut être null par défaut
                'date',
                function ($attribute, $value, $fail) {
                    if ($this->demarrage === 'A_partir_de' && empty($value)) {
                        $fail('Le champ "A partir de" est obligatoire si le démarrage n\'est pas immédiat.');
                    }
                }
            ],
            'description_offre'=>'required'
        ];
    }

    public function messages(){
        return [
            'site_id.required'=>'Le site est Obligatoire',
            'poste_id.required'=>'Le poste est obigatoire',
            'nombre_personne_recherche.required'=>'Nombre de personne obligatoire',
            'description_offre.required'=>'La description est obligatoire',
            'demarrage.required'=>'Obligatoire'
        ];

    }
}
