<?php
// app/Services/MaterialService.php

namespace App\Services;

use App\Repositories\MaterialRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaterialService
{
    protected $repository;

    public function __construct(MaterialRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createMaterial(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Validar que no exista clave duplicada
            $existe = $this->repository->findByClave($data['clave_material'], $data['id_lugar']);
            
            if ($existe) {
                throw new \Exception('Ya existe un material con esa clave en el lugar seleccionado');
            }

            // Validaciones de negocio
            $this->validateBusinessRules($data);

            // Crear material
            $material = $this->repository->create($data);

            Log::info('Material creado', [
                'material_id' => $material->id_material,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Material creado exitosamente',
                'data' => $material,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear material', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear material: ' . $e->getMessage(),
            ];
        }
    }

    public function updateMaterial(int $id, array $data): array
    {
        DB::beginTransaction();
        
        try {
            $material = $this->repository->findById($id);
            
            if (!$material) {
                throw new \Exception('Material no encontrado');
            }

            // Validar clave única (excluyendo el material actual)
            $existe = $this->repository->findByClave($data['clave_material'], $data['id_lugar']);
            
            if ($existe && $existe->id_material != $id) {
                throw new \Exception('Ya existe otro material con esa clave en el lugar seleccionado');
            }

            $this->validateBusinessRules($data);

            $updated = $this->repository->update($id, $data);

            if (!$updated) {
                throw new \Exception('No se pudo actualizar el material');
            }

            Log::info('Material actualizado', [
                'material_id' => $id,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Material actualizado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al actualizar material: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteMaterial(int $id): array
    {
        DB::beginTransaction();
        
        try {
            $material = $this->repository->findById($id);
            
            if (!$material) {
                throw new \Exception('Material no encontrado');
            }

            // Validar que no tenga existencia
            if ($material->existencia > 0) {
                throw new \Exception('No se puede eliminar un material con existencia. Existencia actual: ' . $material->existencia);
            }

            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new \Exception('No se pudo eliminar el material');
            }

            Log::warning('Material eliminado', [
                'material_id' => $id,
                'material' => $material->descripcion,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Material eliminado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al eliminar material: ' . $e->getMessage(),
            ];
        }
    }

    public function aumentarExistencia(int $id, int $cantidad): array
    {
        DB::beginTransaction();
        
        try {
            if ($cantidad <= 0) {
                throw new \Exception('La cantidad debe ser mayor a 0');
            }

            $result = $this->repository->aumentarExistencia($id, $cantidad);

            if (!$result) {
                throw new \Exception('No se pudo aumentar la existencia');
            }

            Log::info('Existencia aumentada', [
                'material_id' => $id,
                'cantidad' => $cantidad,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Se agregaron {$cantidad} unidades exitosamente",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    protected function validateBusinessRules(array $data): void
    {
        if (isset($data['existencia']) && $data['existencia'] < 0) {
            throw new \Exception('La existencia no puede ser negativa');
        }

        if (isset($data['costo_promedio']) && $data['costo_promedio'] <= 0) {
            throw new \Exception('El costo debe ser mayor a 0');
        }

        if (empty($data['id_lugar'])) {
            throw new \Exception('Debe seleccionar un lugar');
        }
    }
}