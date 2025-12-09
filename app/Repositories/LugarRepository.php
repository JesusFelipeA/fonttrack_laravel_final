<?php
// app/Repositories/LugarRepository.php

namespace App\Repositories;

use App\DAO\Interfaces\LugarDAOInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LugarRepository
{
    protected $dao;

    public function __construct(LugarDAOInterface $dao)
    {
        $this->dao = $dao;
    }

    public function getAll(): Collection
    {
        return $this->dao->getAll();
    }

    public function getPaginated(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = \App\Models\Lugar::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('direccion', 'like', "%{$search}%")
                  ->orWhere('responsable', 'like', "%{$search}%");
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

    public function findByNombre(string $nombre)
    {
        return $this->dao->findByNombre($nombre);
    }

    public function getLugaresActivos(): Collection
    {
        return $this->dao->getLugaresActivos();
    }

    public function getEstadisticas(int $id): array
    {
        $lugar = $this->dao->getWithMateriales($id);
        
        if (!$lugar) {
            return [];
        }

        $materiales = $lugar->materiales;
        
        return [
            'total_materiales' => $materiales->count(),
            'total_existencia' => $materiales->sum('existencia'),
            'valor_inventario' => $materiales->sum(function($m) {
                return $m->existencia * $m->costo_promedio;
            }),
            'materiales_agotados' => $materiales->where('existencia', '<=', 0)->count(),
        ];
    }

    public function getUsuariosDelLugar(int $id): Collection
    {
        $lugar = $this->dao->getWithUsuarios($id);
        return $lugar ? $lugar->usuarios : collect([]);
    }
}