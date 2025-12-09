<?php
// app/DAO/Implementations/MaterialDAO.php

namespace App\DAO\Implementations;

use App\DAO\Interfaces\MaterialDAOInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialDAO implements MaterialDAOInterface
{
    protected $model;

    public function __construct(Material $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        try {
            return $this->model->all();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::getAll', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findById(int $id): ?Model
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::findById', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $material = $this->model->create($data);
            DB::commit();
            
            Log::info('Material creado en DAO', ['id' => $material->id_material]);
            return $material;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en MaterialDAO::create', ['data' => $data, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        DB::beginTransaction();
        
        try {
            $material = $this->findById($id);
            
            if (!$material) {
                throw new \Exception("Material con ID {$id} no encontrado");
            }
            
            $updated = $material->update($data);
            DB::commit();
            
            Log::info('Material actualizado en DAO', ['id' => $id]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en MaterialDAO::update', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $material = $this->findById($id);
            
            if (!$material) {
                throw new \Exception("Material con ID {$id} no encontrado");
            }
            
            $deleted = $material->delete();
            DB::commit();
            
            Log::info('Material eliminado en DAO', ['id' => $id]);
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en MaterialDAO::delete', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findBy(string $field, $value): Collection
    {
        try {
            return $this->model->where($field, $value)->get();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::findBy', ['field' => $field, 'value' => $value, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getWithRelations(array $relations): Collection
    {
        try {
            return $this->model->with($relations)->get();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::getWithRelations', ['relations' => $relations, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::count', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function exists(int $id): bool
    {
        try {
            return $this->model->where('id_material', $id)->exists();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::exists', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    // Métodos específicos de Material
    
    public function getByLugar(int $idLugar): Collection
    {
        try {
            return $this->model->where('id_lugar', $idLugar)->get();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::getByLugar', ['id_lugar' => $idLugar, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findByClave(string $clave, ?int $idLugar = null): ?Model
    {
        try {
            $query = $this->model->where('clave_material', $clave);
            
            if ($idLugar) {
                $query->where('id_lugar', $idLugar);
            }
            
            return $query->first();
        } catch (\Exception $e) {
            Log::error('Error en MaterialDAO::findByClave', ['clave' => $clave, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateExistencia(int $id, int $cantidad): bool
    {
        DB::beginTransaction();
        
        try {
            $material = $this->findById($id);
            
            if (!$material) {
                throw new \Exception("Material con ID {$id} no encontrado");
            }
            
            $material->existencia += $cantidad;
            $updated = $material->save();
            
            DB::commit();
            
            Log::info('Existencia actualizada en DAO', [
                'id' => $id,
                'cantidad' => $cantidad,
                'nueva_existencia' => $material->existencia
            ]);
            
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en MaterialDAO::updateExistencia', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}