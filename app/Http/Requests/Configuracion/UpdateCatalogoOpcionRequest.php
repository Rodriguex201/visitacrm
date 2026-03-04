<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatalogoOpcionRequest extends FormRequest
{
    private const CATEGORIAS = [
        'estado-actual',
        'aplicativos',
        'procesos-electronicos',
        'equipos',
        'como-llego',
        'cotizaciones',
    ];

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
        return [
            'categoria' => ['required', 'string', Rule::in(self::CATEGORIAS)],
            'nombre' => ['required', 'string', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
            'valor' => ['nullable', 'numeric', 'between:0,9999999999.99'],
        ];
    }
}
