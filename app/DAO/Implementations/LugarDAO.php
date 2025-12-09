<?php
// app/DAO/Implementations/LugarDAO.php

namespace App\DAO\Implementations;

use App\DAO\Interfaces\LugarDAOInterface;
use App\Models\Lugar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LugarDAO implements LugarDAOInterface
{
    protected $model;

    public function __construct(Lugar $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        try {
            return $this->model->all();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::getAll', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findById(int $id): ?Model
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::findById', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $lugar = $this->model->create($data);
            DB::commit();
            
            Log::info('Lugar creado en DAO', ['id' => $lugar->id_lugar]);
            return $lugar;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en LugarDAO::create', ['data' => $data, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        DB::beginTransaction();
        
        try {
            $lugar = $this->findById($id);
            
            if (!$lugar) {
                throw new \Exception("Lugar con ID {$id} no encontrado");
            }
            
            $updated = $lugar->update($data);
            DB::commit();
            
            Log::info('Lugar actualizado en DAO', ['id' => $id]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en LugarDAO::update', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $lugar = $this->findById($id);
            
            if (!$lugar) {
                throw new \Exception("Lugar con ID {$id} no encontrado");
            }
            
            $deleted = $lugar->delete();
            DB::commit();
            
            Log::info('Lugar eliminado en DAO', ['id' => $id]);
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en LugarDAO::delete', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findBy(string $field, $value): Collection
    {
        try {
            return $this->model->where($field, $value)->get();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::findBy', ['field' => $field, 'value' => $value, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::count', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function exists(int $id): bool
    {
        try {
            return $this->model->where('id_lugar', $id)->exists();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::exists', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findByNombre(string $nombre): ?Model
    {
        try {
            return $this->model->where('nombre', $nombre)->first();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::findByNombre', ['nombre' => $nombre, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getLugaresActivos(): Collection
    {
        try {
            // Si tienes un campo 'activo' en la tabla
            // return $this->model->where('activo', 1)->get();
            
            // Si no, devuelve todos
            return $this->model->orderBy('nombre')->get();
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::getLugaresActivos', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getWithMateriales(int $id): ?Model
    {
        try {
            return $this->model->with('materiales')->find($id);
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::getWithMateriales', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getWithUsuarios(int $id): ?Model
    {
        try {
            return $this->model->with('usuarios')->find($id);
        } catch (\Exception $e) {
            Log::error('Error en LugarDAO::getWithUsuarios', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}