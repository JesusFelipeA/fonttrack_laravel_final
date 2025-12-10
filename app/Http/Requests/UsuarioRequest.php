<?php
// app/Http/Requests/UsuarioRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $usuarioId = $this->route('usuario') ?? $this->route('id');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'nombre' => 'required|string|max:100',
            'tipo_usuario' => 'required|in:1,2',
            'id_lugar' => 'required|exists:lugares,id_lugar',
        ];

        // Campo email o correo (dependiendo de tu modelo)
        if ($this->has('email')) {
            $rules['email'] = 'required|email|unique:users,email' . ($usuarioId ? ',' . $usuarioId : '');
        } else {
            $rules['correo'] = 'required|email|unique:users,correo' . ($usuarioId ? ',' . $usuarioId : '');
        }

        // Password obligatorio al crear, opcional al editar
        if (!$isUpdate) {
            $rules['password'] = 'required|string|min:6';
        } else {
            $rules['password'] = 'nullable|string|min:6';
        }

        // Foto opcional
        $rules['foto_usuario'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';

        return $rules;
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo debe ser una dirección válida',
            'email.unique' => 'Este correo ya está registrado',
            'correo.required' => 'El correo electrónico es obligatorio',
            'correo.email' => 'El correo debe ser una dirección válida',
            'correo.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'tipo_usuario.required' => 'El tipo de usuario es obligatorio',
            'tipo_usuario.in' => 'El tipo de usuario debe ser Admin o Usuario',
            'id_lugar.required' => 'Debes seleccionar un lugar',
            'id_lugar.exists' => 'El lugar seleccionado no existe',
            'foto_usuario.image' => 'El archivo debe ser una imagen',
            'foto_usuario.mimes' => 'La imagen debe ser JPG, JPEG o PNG',
            'foto_usuario.max' => 'La imagen no debe superar 2MB',
        ];
    }
}