<?php

namespace App\DAO\Interfaces;

interface UsuarioDAOInterface
{
    /**
     * Obtiene todos los usuarios
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerTodos();

    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $id
     * @return \App\Models\Usuario|null
     */
    public function obtenerPorId($id);

    /**
     * Obtiene un usuario por su email
     * 
     * @param string $email
     * @return \App\Models\Usuario|null
     */
    public function obtenerPorEmail($email);

    /**
     * Crea un nuevo usuario
     * 
     * @param array $datos
     * @return \App\Models\Usuario
     */
    public function crear(array $datos);

    /**
     * Actualiza un usuario existente
     * 
     * @param int $id
     * @param array $datos
     * @return \App\Models\Usuario|bool
     */
    public function actualizar($id, array $datos);

    /**
     * Elimina un usuario
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar($id);

    /**
     * Obtiene usuarios por rol
     * 
     * @param string $rol
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerPorRol($rol);

    /**
     * Busca usuarios por término
     * 
     * @param string $termino
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscar($termino);

    /**
     * Cambia el estado de un usuario (activo/inactivo)
     * 
     * @param int $id
     * @param bool $activo
     * @return \App\Models\Usuario|bool
     */
    public function cambiarEstado($id, $activo);

    /**
     * Verifica si un email ya existe
     * 
     * @param string $email
     * @param int|null $exceptoId
     * @return bool
     */
    public function emailExiste($email, $exceptoId = null);

    /**
     * Obtiene usuarios activos
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerActivos();

    /**
     * Obtiene la cantidad total de usuarios
     * 
     * @return int
     */
    public function contar();

    /**
     * Obtiene usuarios paginados
     * 
     * @param int $porPagina
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerPaginados($porPagina = 15);

    public function getAll();
    public function findById();
    public function create();
    public function update();
    public function delete();
    public function findByEmail();
    public function findByCorreo();
    public function getByLugar();
    public function getAdministradores ();
    public function verificarPassword ();
}