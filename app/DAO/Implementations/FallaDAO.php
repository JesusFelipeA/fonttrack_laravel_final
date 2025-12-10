<?php
// app/DAO/Implementations/ReporteFallaDAO.php

namespace App\DAO\Implementations;

use App\DAO\Interfaces\ReporteFallaDAOInterface;
use App\Models\Falla;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FallaDAO implements ReporteFallaDAOInterface
{
    protected $model;

    public function __construct(Falla $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        try {
            return $this->model
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getAll', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function findById(int $id): ?Model
    {
        try {
            return $this->model
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->find($id);
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::findById', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->model->create($data);
            DB::commit();
            
            Log::info('Reporte de falla creado en DAO', ['id' => $reporte->id_falla]);
            return $reporte;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::create', ['data' => $data, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }
            
            $updated = $reporte->update($data);
            DB::commit();
            
            Log::info('Reporte actualizado en DAO', ['id' => $id]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::update', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }
            
            $deleted = $reporte->delete();
            DB::commit();
            
            Log::info('Reporte eliminado en DAO', ['id' => $id]);
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::delete', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::count', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByLugar(int $idLugar): Collection
    {
        try {
            return $this->model
                ->where('id_lugar', $idLugar)
                ->with(['usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getByLugar', ['id_lugar' => $idLugar, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByVehiculo(string $eco): Collection
    {
        try {
            return $this->model
                ->where('eco', $eco)
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getByVehiculo', ['eco' => $eco, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByUsuarioReporta(int $idUsuario): Collection
    {
        try {
            return $this->model
                ->where('usuario_reporta_id', $idUsuario)
                ->with(['lugar', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getByUsuarioReporta', ['id_usuario' => $idUsuario, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByUsuarioRevisa(int $idUsuario): Collection
    {
        try {
            return $this->model
                ->where('usuario_revisa_id', $idUsuario)
                ->with(['lugar', 'usuarioReporta', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getByUsuarioRevisa', ['id_usuario' => $idUsuario, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getPendientes(int $idLugar): Collection
    {
        try {
            return $this->model
                ->where('id_lugar', $idLugar)
                ->where('estado', 'pendiente')
                ->with(['usuarioReporta', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getPendientes', ['id_lugar' => $idLugar, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getAprobados(int $idLugar): Collection
    {
        try {
            return $this->model
                ->where('id_lugar', $idLugar)
                ->where('estado', 'aprobado')
                ->with(['usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getAprobados', ['id_lugar' => $idLugar, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getRechazados(int $idLugar): Collection
    {
        try {
            return $this->model
                ->where('id_lugar', $idLugar)
                ->where('estado', 'rechazado')
                ->with(['usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getRechazados', ['id_lugar' => $idLugar, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function aprobar(int $id, array $data): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }

            $data['estado'] = 'aprobado';
            $data['fecha_aprobacion'] = now();
            
            $updated = $reporte->update($data);
            DB::commit();
            
            Log::info('Reporte aprobado en DAO', ['id' => $id]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::aprobar', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function rechazar(int $id, array $data): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }

            $data['estado'] = 'rechazado';
            $data['fecha_rechazo'] = now();
            
            $updated = $reporte->update($data);
            DB::commit();
            
            Log::info('Reporte rechazado en DAO', ['id' => $id]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::rechazar', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }

            $estadosValidos = ['pendiente', 'aprobado', 'rechazado', 'en_revision', 'completado'];
            
            if (!in_array($estado, $estadosValidos)) {
                throw new \Exception("Estado '{$estado}' no válido");
            }

            $updated = $reporte->update(['estado' => $estado]);
            DB::commit();
            
            Log::info('Estado de reporte cambiado en DAO', ['id' => $id, 'estado' => $estado]);
            return $updated;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::cambiarEstado', ['id' => $id, 'estado' => $estado, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function attachMateriales(int $id, array $materiales): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }

            // Sincronizar materiales (reemplaza los existentes)
            $reporte->materiales()->sync($materiales);
            DB::commit();
            
            Log::info('Materiales adjuntados al reporte en DAO', ['id' => $id, 'materiales' => $materiales]);
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::attachMateriales', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function detachMateriales(int $id, array $materiales = []): bool
    {
        DB::beginTransaction();
        
        try {
            $reporte = $this->findById($id);
            
            if (!$reporte) {
                throw new \Exception("Reporte con ID {$id} no encontrado");
            }

            // Si no se especifican materiales, desvincula todos
            if (empty($materiales)) {
                $reporte->materiales()->detach();
            } else {
                $reporte->materiales()->detach($materiales);
            }
            
            DB::commit();
            
            Log::info('Materiales desvinculados del reporte en DAO', ['id' => $id]);
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en ReporteFallaDAO::detachMateriales', ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getByEstado(string $estado): Collection
    {
        try {
            return $this->model
                ->where('estado', $estado)
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getByEstado', ['estado' => $estado, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getEstadisticas(): array
    {
        try {
            return [
                'total' => $this->model->count(),
                'pendientes' => $this->model->where('estado', 'pendiente')->count(),
                'aprobados' => $this->model->where('estado', 'aprobado')->count(),
                'rechazados' => $this->model->where('estado', 'rechazado')->count(),
                'en_revision' => $this->model->where('estado', 'en_revision')->count(),
                'completados' => $this->model->where('estado', 'completado')->count(),
                'por_lugar' => $this->model
                    ->select('id_lugar', DB::raw('count(*) as total'))
                    ->groupBy('id_lugar')
                    ->with('lugar:id_lugar,nombre')
                    ->get()
                    ->toArray(),
            ];
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getEstadisticas', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function buscar(string $termino): Collection
    {
        try {
            return $this->model
                ->where(function($query) use ($termino) {
                    $query->where('descripcion', 'LIKE', "%{$termino}%")
                          ->orWhere('eco', 'LIKE', "%{$termino}%")
                          ->orWhere('observaciones', 'LIKE', "%{$termino}%");
                })
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::buscar', ['termino' => $termino, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getPaginados(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        try {
            return $this->model
                ->with(['lugar', 'usuarioReporta', 'usuarioRevisa', 'materiales'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Error en ReporteFallaDAO::getPaginados', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}