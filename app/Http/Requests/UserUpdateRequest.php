<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        // $id = $this->route('user'); // Récupère l'ID pour ignorer l'utilisateur actuel dans la validation unique
        return [
            // 'user_type' => 'required|in:manager,controller,user,customer',
            // 'company_id' => 'required|exists:companies,id',
            'n_social' => 'nullable|string',
            'name' => 'required|string',
            'first_name' => 'required|string',
            // 'username' => 'nullable|string|unique:users,username,' . $id,
            // 'email' => 'required|string|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'employee_function' => 'nullable|exists:fonctions,id',
            'qualification' => 'nullable|string',
            'employee_quotient' => 'nullable|string',
            'identity_document_type' => 'nullable|in:cni,passport,driving_licenses',
            'id_number' => 'nullable|string',
            'address' => 'nullable|string',
            'salary_rate' => 'nullable|string',
            'avatar' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_email' => 'nullable|string|email',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'emergency_contact_address' => 'nullable|string',
            'customer_type' => 'nullable|in:physical_person,legal_person',

            // Uniquement pour les employés
            'monday_checked' => 'sometimes|boolean',
            'start_time_monday' => 'nullable|date_format:H:i|required_if:monday_checked,true',
            'end_time_monday' => 'nullable|date_format:H:i|required_if:monday_checked,true|after:start_time_monday',

            'tuesday_checked' => 'sometimes|boolean',
            'start_time_tuesday' => 'nullable|date_format:H:i|required_if:tuesday_checked,true',
            'end_time_tuesday' => 'nullable|date_format:H:i|required_if:tuesday_checked,true|after:start_time_tuesday',

            'wednesday_checked' => 'sometimes|boolean',
            'start_time_wednesday' => 'nullable|date_format:H:i|required_if:wednesday_checked,true',
            'end_time_wednesday' => 'nullable|date_format:H:i|required_if:wednesday_checked,true|after:start_time_wednesday',

            'thursday_checked' => 'sometimes|boolean',
            'start_time_thursday' => 'nullable|date_format:H:i|required_if:thursday_checked,true',
            'end_time_thursday' => 'nullable|date_format:H:i|required_if:thursday_checked,true|after:start_time_thursday',

            'friday_checked' => 'sometimes|boolean',
            'start_time_friday' => 'nullable|date_format:H:i|required_if:friday_checked,true',
            'end_time_friday' => 'nullable|date_format:H:i|required_if:friday_checked,true|after:start_time_friday',

            'saturday_checked' => 'sometimes|boolean',
            'start_time_saturday' => 'nullable|date_format:H:i|required_if:saturday_checked,true',
            'end_time_saturday' => 'nullable|date_format:H:i|required_if:saturday_checked,true|after:start_time_saturday',

            'sunday_checked' => 'sometimes|boolean',
            'start_time_sunday' => 'nullable|date_format:H:i|required_if:sunday_checked,true',
            'end_time_sunday' => 'nullable|date_format:H:i|required_if:sunday_checked,true|after:start_time_sunday',
        ];
    }
}
