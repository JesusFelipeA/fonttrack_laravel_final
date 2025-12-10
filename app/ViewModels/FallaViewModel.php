<?php

namespace App\ViewModels;

use App\DAO\Implementations\FallaDAO;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FallaViewModel
{
    protected $fallaDAO;

    public function __construct(FallaDAO $fallaDAO)
    {
        $this->fallaDAO = $fallaDAO;
    }

    /**
     * Obtener todas las fallas del sistema
     */
    public function obtenerTodas()
    {
        try {
            $fallas = $this->fallaDAO->getListData();
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => 'Fallas obtenidas correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerTodas', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener las fallas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener falla por ID
     */
    public function obtenerPorId($id)
    {
        try {
            $falla = $this->fallaDAO->obtenerPorId($id);
            
            if (!$falla) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Falla no encontrada'
                ];
            }

            return [
                'success' => true,
                'data' => $this->formatearFalla($falla),
                'message' => 'Falla obtenida correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerPorId', ['id' => $id, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error al obtener la falla: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear nueva falla
     */
    public function crear(array $datos)
    {
        $validacion = $this->validarDatos($datos);
        
        if (!$validacion['success']) {
            return $validacion;
        }

        try {
            // Establecer valores por defecto
            $datos['estado'] = $datos['estado'] ?? 'pendiente';
            $datos['fecha_reporte'] = $datos['fecha_reporte'] ?? now();
            
            $falla = $this->fallaDAO->crear($datos);
            
            return [
                'success' => true,
                'data' => $this->formatearFalla($falla),
                'message' => 'Falla creada correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::crear', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error al crear la falla: ' . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ];
        }
    }

    /**
     * Actualizar falla existente
     */
    public function actualizar($id, array $datos)
    {
        $validacion = $this->validarDatos($datos, $id);
        
        if (!$validacion['success']) {
            return $validacion;
        }

        try {
            $falla = $this->fallaDAO->actualizar($id, $datos);
            
            if (!$falla) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Falla no encontrada para actualizar'
                ];
            }

            return [
                'success' => true,
                'data' => $this->formatearFalla($falla),
                'message' => 'Falla actualizada correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::actualizar', ['id' => $id, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error al actualizar la falla: ' . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ];
        }
    }

    /**
     * Eliminar falla
     */
    public function eliminar($id)
    {
        try {
            $resultado = $this->fallaDAO->eliminar($id);
            
            if (!$resultado) {
                return [
                    'success' => false,
                    'message' => 'Falla no encontrada para eliminar'
                ];
            }

            return [
                'success' => true,
                'message' => 'Falla eliminada correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::eliminar', ['id' => $id, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al eliminar la falla: ' . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ];
        }
    }

    /**
     * Obtener fallas por estado
     */
    public function obtenerPorEstado($estado)
    {
        try {
            if (!$this->esEstadoValido($estado)) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => "Estado inválido: {$estado}"
                ];
            }

            $fallas = $this->fallaDAO->obtenerPorEstado($estado);
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => "Fallas con estado '{$estado}' obtenidas correctamente"
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerPorEstado', ['estado' => $estado, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener fallas por estado: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener fallas por usuario
     */
    public function obtenerPorUsuario($usuario_id)
    {
        try {
            $fallas = $this->fallaDAO->obtenerPorUsuario($usuario_id);
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => 'Fallas del usuario obtenidas correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerPorUsuario', ['usuario_id' => $usuario_id, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener fallas del usuario: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener fallas por lugar
     */
    public function obtenerPorLugar($lugar_id)
    {
        try {
            $fallas = $this->fallaDAO->obtenerPorLugar($lugar_id);
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => 'Fallas del lugar obtenidas correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerPorLugar', ['lugar_id' => $lugar_id, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener fallas del lugar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener fallas por prioridad
     */
    public function obtenerPorPrioridad($prioridad)
    {
        try {
            if (!$this->esPrioridadValida($prioridad)) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => "Prioridad inválida: {$prioridad}"
                ];
            }

            $fallas = $this->fallaDAO->obtenerPorPrioridad($prioridad);
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => "Fallas con prioridad '{$prioridad}' obtenidas correctamente"
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerPorPrioridad', ['prioridad' => $prioridad, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener fallas por prioridad: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar estado de una falla
     */
    public function cambiarEstado($id, $estado)
    {
        if (!$this->esEstadoValido($estado)) {
            return [
                'success' => false,
                'message' => "Estado inválido: {$estado}. Estados válidos: pendiente, en_proceso, resuelta, cancelada"
            ];
        }

        try {
            $falla = $this->fallaDAO->actualizar($id, ['estado' => $estado]);
            
            if (!$falla) {
                return [
                    'success' => false,
                    'message' => 'Falla no encontrada'
                ];
            }

            return [
                'success' => true,
                'data' => $this->formatearFalla($falla),
                'message' => "Estado de falla cambiado a '{$estado}' correctamente"
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::cambiarEstado', ['id' => $id, 'estado' => $estado, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ];
        }
    }

    /**
     * Obtener estadísticas de fallas
     */
    public function obtenerEstadisticas()
    {
        try {
            $total = $this->fallaDAO->contar();
            $pendientes = count($this->fallaDAO->obtenerPorEstado('pendiente') ?? []);
            $enProceso = count($this->fallaDAO->obtenerPorEstado('en_proceso') ?? []);
            $resueltas = count($this->fallaDAO->obtenerPorEstado('resuelta') ?? []);
            $canceladas = count($this->fallaDAO->obtenerPorEstado('cancelada') ?? []);

            return [
                'success' => true,
                'data' => [
                    'total' => $total,
                    'pendientes' => $pendientes,
                    'en_proceso' => $enProceso,
                    'resueltas' => $resueltas,
                    'canceladas' => $canceladas,
                    'porcentaje_resolucion' => $total > 0 ? round(($resueltas / $total) * 100, 2) : 0
                ],
                'message' => 'Estadísticas obtenidas correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::obtenerEstadisticas', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Buscar fallas por término
     */
    public function buscar($termino)
    {
        try {
            if (empty($termino)) {
                return [
                    'success' => false,
                    'data' => [],
                    'message' => 'Término de búsqueda vacío'
                ];
            }

            $fallas = $this->fallaDAO->buscar($termino);
            
            return [
                'success' => true,
                'data' => array_map([$this, 'formatearFalla'], $fallas ?? []),
                'message' => 'Búsqueda realizada correctamente'
            ];
        } catch (\Exception $e) {
            Log::error('Error en FallaViewModel::buscar', ['termino' => $termino, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error al buscar fallas: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener fallas pendientes
     */
    public function obtenerPendientes()
    {
        return $this->obtenerPorEstado('pendiente');
    }

    /**
     * Obtener fallas en proceso
     */
    public function obtenerEnProceso()
    {
        return $this->obtenerPorEstado('en_proceso');
    }

    /**
     * Obtener fallas resueltas
     */
    public function obtenerResueltas()
    {
        return $this->obtenerPorEstado('resuelta');
    }

    /**
     * Validar datos de entrada
     */
    private function validarDatos(array $datos, $id = null)
    {
        $reglas = [
            'descripcion' => 'required|string|min:10|max:1000',
            'estado' => 'nullable|in:pendiente,en_proceso,resuelta,cancelada',
            'prioridad' => 'nullable|in:baja,media,alta',
            'usuario_id' => 'required|integer|exists:tb_users,id_usuario',
            'lugar_id' => 'required|integer|exists:tb_lugares,id_lugar',
            'fecha_reporte' => 'nullable|date',
        ];

        $mensajes = [
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'descripcion.max' => 'La descripción no debe exceder 1000 caracteres',
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario seleccionado no existe',
            'lugar_id.required' => 'El lugar es obligatorio',
            'lugar_id.exists' => 'El lugar seleccionado no existe',
            'estado.in' => 'El estado seleccionado es inválido',
            'prioridad.in' => 'La prioridad seleccionada es inválida',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Errores en la validación de datos',
                'errors' => $validator->errors()->toArray()
            ];
        }

        return ['success' => true];
    }

    /**
     * Formatear datos de falla para presentación
     */
    private function formatearFalla($falla)
    {
        return [
            'id' => $falla->id ?? $falla['id'] ?? null,
            'descripcion' => $falla->descripcion ?? $falla['descripcion'] ?? '',
            'estado' => $falla->estado ?? $falla['estado'] ?? 'pendiente',
            'estado_etiqueta' => $this->obtenerEtiquetaEstado($falla->estado ?? $falla['estado'] ?? 'pendiente'),
            'prioridad' => $falla->prioridad ?? $falla['prioridad'] ?? 'media',
            'prioridad_etiqueta' => $this->obtenerEtiquetaPrioridad($falla->prioridad ?? $falla['prioridad'] ?? 'media'),
            'usuario_id' => $falla->usuario_id ?? $falla['usuario_id'] ?? null,
            'lugar_id' => $falla->lugar_id ?? $falla['lugar_id'] ?? null,
            'fecha_reporte' => $falla->fecha_reporte ?? $falla['fecha_reporte'] ?? now(),
            'fecha_creacion' => $falla->created_at ?? $falla['created_at'] ?? now(),
        ];
    }

    /**
     * Obtener etiqueta de prioridad
     */
    private function obtenerEtiquetaPrioridad($prioridad)
    {
        $etiquetas = [
            'baja' => 'Baja Prioridad',
            'media' => 'Prioridad Media',
            'alta' => 'Alta Prioridad',
        ];

        return $etiquetas[$prioridad] ?? $prioridad;
    }

    /**
     * Obtener etiqueta de estado
     */
    private function obtenerEtiquetaEstado($estado)
    {
        $etiquetas = [
            'pendiente' => 'Pendiente',
            'en_proceso' => 'En Proceso',
            'resuelta' => 'Resuelta',
            'cancelada' => 'Cancelada',
        ];

        return $etiquetas[$estado] ?? $estado;
    }

    /**
     * Validar que estado sea correcto
     */
    private function esEstadoValido($estado)
    {
        return in_array($estado, ['pendiente', 'en_proceso', 'resuelta', 'cancelada']);
    }

    /**
     * Validar que prioridad sea correcta
     */
    private function esPrioridadValida($prioridad)
    {
        return in_array($prioridad, ['baja', 'media', 'alta']);
    }
}