<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogoSidebarRequest extends FormRequest
{
    protected $errorBag = 'updateLogoSidebar';

    public function authorize(): bool
    {
        return $this->user()?->tipo_usuario === 'administracion';
    }

    public function rules(): array
    {
        return [
            'logo' => ['required', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Selecciona un archivo para el logo.',
            'logo.image' => 'El archivo debe ser una imagen válida.',
            'logo.mimes' => 'El logo debe estar en formato PNG, JPG, JPEG o WEBP.',
            'logo.max' => 'El logo no puede superar los 2MB.',
        ];
    }
}
