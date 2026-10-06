<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Demande;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStatutDemandeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'statut' => [
                'required',
                'string',
                Rule::in([
                    Demande::STATUT_EN_COURS,
                    Demande::STATUT_VALIDEE,
                    Demande::STATUT_REJETEE,
                ]),
            ],
            'motif_rejet' => [
                Rule::requiredIf(fn () => $this->input('statut') === Demande::STATUT_REJETEE),
                'nullable',
                'string',
                'min:5',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'statut.required' => 'Le statut cible est obligatoire.',
            'statut.in' => 'Le statut fourni est invalide.',
            'motif_rejet.required_if' => 'Un rejet doit obligatoirement être motivé.',
            'motif_rejet.min' => 'Le motif de rejet doit comporter au moins 5 caractères.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Transition de statut refusée : données non valides.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
