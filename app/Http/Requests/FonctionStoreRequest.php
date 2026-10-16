<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FonctionStoreRequest extends FormRequest
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
            'nom_fonction'=>'|required|unique:fonctions,nom_fonction,id',
            'description_fonction'=>'nullable'
        ];
    }

    public function messages(){
        return[
            'nom_fonction.required'=>'Le Nom de la Fonction est Obligatoire avant la description',
            'nom_fonction.unique'=>'Le Nom de la fonction existe déja dans la base',
        ];
    }
}
