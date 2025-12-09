<?php
// app/Http/Requests/LugarRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LugarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100',
            'estado' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del lugar es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'estado.required' => 'El estado del lugar es obligatorio',
            'estado.max' => 'El estado no puede exceder 255 caracteres',
        ];
    }
}