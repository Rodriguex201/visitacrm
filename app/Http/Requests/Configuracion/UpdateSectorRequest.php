<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tipo_usuario === 'administracion';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => trim((string) $this->input('nombre')),
        ]);
    }

    public function rules(): array
    {
        $sector = $this->route('sector');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sectores', 'nombre')->ignore($sector?->id),
            ],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
