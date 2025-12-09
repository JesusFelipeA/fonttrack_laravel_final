<?php
// app/Repositories/MaterialRepository.php

namespace App\Repositories;

use App\DAO\Interfaces\MaterialDAOInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MaterialRepository
{
    protected $dao;

    public function __construct(MaterialDAOInterface $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Obtener materiales filtrados con paginación
     */
    public function getFiltered(?int $idLugar = null, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = \App\Models\Material::query()->with('lugar');

        if ($idLugar) {
            $query->where('id_lugar', $idLugar);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('clave_material', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('generico', 'like', "%{$search}%")
                  ->orWhere('clasificacion', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('clave_material')->paginate($perPage);
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

    public function aumentarExistencia(int $id, int $cantidad): bool
    {
        return $this->dao->updateExistencia($id, $cantidad);
    }

    public function getByLugar(int $idLugar): Collection
    {
        return $this->dao->getByLugar($idLugar);
    }

    public function findByClave(string $clave, ?int $idLugar = null)
    {
        return $this->dao->findByClave($clave, $idLugar);
    }
}