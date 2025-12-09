<?php
// app/Services/LugarService.php

namespace App\Services;

use App\Repositories\LugarRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LugarService
{
    protected $repository;

    public function __construct(LugarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createLugar(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // Validar que no exista nombre duplicado
            $existe = $this->repository->findByNombre($data['nombre']);
            
            if ($existe) {
                throw new \Exception('Ya existe un lugar con ese nombre');
            }

            $lugar = $this->repository->create($data);

            Log::info('Lugar creado', [
                'lugar_id' => $lugar->id_lugar,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lugar creado exitosamente',
                'data' => $lugar,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear lugar', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'message' => 'Error al crear lugar: ' . $e->getMessage(),
            ];
        }
    }

    public function updateLugar(int $id, array $data): array
    {
        DB::beginTransaction();
        
        try {
            $lugar = $this->repository->findById($id);
            
            if (!$lugar) {
                throw new \Exception('Lugar no encontrado');
            }

            // Validar nombre único (excluyendo el lugar actual)
            $existe = $this->repository->findByNombre($data['nombre']);
            
            if ($existe && $existe->id_lugar != $id) {
                throw new \Exception('Ya existe otro lugar con ese nombre');
            }

            $updated = $this->repository->update($id, $data);

            if (!$updated) {
                throw new \Exception('No se pudo actualizar el lugar');
            }

            Log::info('Lugar actualizado', [
                'lugar_id' => $id,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lugar actualizado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al actualizar lugar: ' . $e->getMessage(),
            ];
        }
    }

    public function deleteLugar(int $id): array
    {
        DB::beginTransaction();
        
        try {
            $lugar = $this->repository->findById($id);
            
            if (!$lugar) {
                throw new \Exception('Lugar no encontrado');
            }

            // Validar que no tenga materiales asociados
            $estadisticas = $this->repository->getEstadisticas($id);
            
            if ($estadisticas['total_materiales'] > 0) {
                throw new \Exception('No se puede eliminar un lugar con materiales asociados. Total de materiales: ' . $estadisticas['total_materiales']);
            }

            // Validar que no tenga usuarios asociados
            $usuarios = $this->repository->getUsuariosDelLugar($id);
            
            if ($usuarios->count() > 0) {
                throw new \Exception('No se puede eliminar un lugar con usuarios asignados. Total de usuarios: ' . $usuarios->count());
            }

            $deleted = $this->repository->delete($id);

            if (!$deleted) {
                throw new \Exception('No se pudo eliminar el lugar');
            }

            Log::warning('Lugar eliminado', [
                'lugar_id' => $id,
                'lugar' => $lugar->nombre,
                'usuario' => auth()->user()->nombre ?? 'Sistema',
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Lugar eliminado exitosamente',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error al eliminar lugar: ' . $e->getMessage(),
            ];
        }
    }
}