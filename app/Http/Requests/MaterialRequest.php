<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // ⬇️ CAMBIAR AQUÍ el nombre de la tabla
        return [
            'clave_material' => 'required|string|max:50',
            'descripcion' => 'required|string|max:255',
            'generico' => 'nullable|string|max:100',
            'clasificacion' => 'nullable|string|max:100',
            'existencia' => 'required|integer|min:0',
            'costo_promedio' => 'required|numeric|min:0.01',
            
            'id_lugar' => 'required|exists:lugares,id_lugar',
        ];
    }

    public function messages()
    {
        return [
            'clave_material.required' => 'La clave del material es obligatoria',
            'descripcion.required' => 'La descripción es obligatoria',
            'existencia.required' => 'La existencia es obligatoria',
            'existencia.min' => 'La existencia no puede ser negativa',
            'costo_promedio.required' => 'El costo es obligatorio',
            'costo_promedio.min' => 'El costo debe ser mayor a 0',
            'id_lugar.required' => 'Debes seleccionar un lugar',
            'id_lugar.exists' => 'El lugar seleccionado no existe',
        ];
    }
}