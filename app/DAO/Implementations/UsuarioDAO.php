<?php

namespace App\DAO;

use App\Models\Usuarios;
use Illuminate\Support\Facades\Hash;

class UsuarioDAO
{
    public function obtenerTodos()
    {
        return Usuarios::all();
    }

    public function obtenerPorId($id)
    {
        return Usuarios::find($id);
    }

    public function obtenerPorEmail($email)
    {
        return Usuarios::where('email', $email)->first();
    }

    public function crear(array $datos)
    {
        if (isset($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        }
        
        return Usuarios::create($datos);
    }

    public function actualizar($id, array $datos)
    {
        $usuario = $this->obtenerPorId($id);
        
        if (!$usuario) {
            return false;
        }

        if (isset($datos['password']) && !empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $usuario->update($datos);
        return $usuario;
    }

    public function eliminar($id)
    {
        $usuario = $this->obtenerPorId($id);
        
        if (!$usuario) {
            return false;
        }

        return $usuario->delete();
    }

    public function obtenerPorRol($rol)
    {
        return Usuarios::where('rol', $rol)->get();
    }

    public function buscar($termino)
    {
        return Usuarios::where('nombre', 'LIKE', "%{$termino}%")
            ->orWhere('email', 'LIKE', "%{$termino}%")
            ->get();
    }

    public function cambiarEstado($id, $activo)
    {
        $usuario = $this->obtenerPorId($id);
        
        if (!$usuario) {
            return false;
        }

        $usuario->activo = $activo;
        $usuario->save();
        
        return $usuario;
    }
}