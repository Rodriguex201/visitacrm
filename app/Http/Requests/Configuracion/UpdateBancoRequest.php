<?php

namespace App\Http\Requests\Configuracion;

use App\Models\Banco;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBancoRequest extends FormRequest
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
        /** @var Banco|null $banco */
        $banco = $this->route('banco');

        return [
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('bancos', 'nombre')->ignore($banco?->id),
            ],
        ];
    }
}
