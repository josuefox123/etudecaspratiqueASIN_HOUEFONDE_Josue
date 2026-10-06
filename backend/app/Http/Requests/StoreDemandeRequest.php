<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Demande;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'npi' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'type_acte' => ['required', 'string', Rule::in(Demande::TYPES_ACTES)],
            'nombre_copies' => ['required', 'integer', 'between:1,5'],
        ];
    }

    public function messages(): array
    {
        return [
            'npi.required' => 'Le NPI est obligatoire.',
            'npi.regex' => 'Le NPI doit comporter exactement 10 chiffres numériques.',
            'type_acte.required' => "Le type d'acte est obligatoire.",
            'type_acte.in' => "Le type d'acte doit être : acte de naissance, casier judiciaire ou certificat de résidence.",
            'nombre_copies.required' => 'Le nombre de copies est obligatoire.',
            'nombre_copies.between' => 'Le nombre de copies doit être compris entre 1 et 5.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Données fournies non valides.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
