<?php
// app/Repositories/UsuarioRepository.php

namespace App\Repositories;

use App\DAO\Interfaces\UsuarioDAOInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository
{
    protected $dao;

    public function __construct(UsuarioDAOInterface $dao)
    {
        $this->dao = $dao;
    }

    public function getAll(): Collection
    {
        return $this->dao->getAll();
    }

    public function getPaginated(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = \App\Models\Usuarios::query()->with('lugar');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nombre')->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->dao->findById($id);
    }

    public function create(array $data)
    {
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->dao->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->dao->delete($id);
    }

    public function findByEmail(string $email)
    {
        return $this->dao->findByEmail($email);
    }

    public function findByCorreo(string $correo)
    {
        return $this->dao->findByCorreo($correo);
    }

    public function getByLugar(int $idLugar): Collection
    {
        return $this->dao->getByLugar($idLugar);
    }

    public function getAdministradores(): Collection
    {
        return $this->dao->getAdministradores();
    }

    public function verificarPassword(int $id, string $password): bool
    {
        return $this->dao->verificarPassword($id, $password);
    }

    public function getEstadisticasUsuario(int $id): array
    {
        $usuario = $this->findById($id);
        
        if (!$usuario) {
            return [];
        }

        // Contar reportes creados por el usuario
        $reportesCreados = \App\Models\Falla::where('usuario_reporta_id', $id)->count();
        
        // Contar reportes revisados por el usuario
        $reportesRevisados = \App\Models\Falla::where('usuario_revisa_id', $id)->count();

        return [
            'reportes_creados' => $reportesCreados,
            'reportes_revisados' => $reportesRevisados,
            'es_admin' => $usuario->tipo_usuario == 1,
            'lugar' => $usuario->lugar ? $usuario->lugar->nombre : 'Sin lugar asignado',
        ];
    }
}