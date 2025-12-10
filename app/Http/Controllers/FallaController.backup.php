<?php

namespace App\Http\Controllers;

use App\ViewModels\FallaViewModel;
use App\ViewModels\UsuarioViewModel;
use App\ViewModels\LugarViewModel;
use App\ViewModels\MaterialViewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FallaController extends Controller
{
    protected $fallaViewModel;
    protected $usuarioViewModel;
    protected $lugarViewModel;
    protected $materialViewModel;

    /**
     * Inyección de dependencias correcta (mejor práctica que new)
     */
    public function __construct(
        //FallaViewModel $fallaViewModel,
        UsuarioViewModel $usuarioViewModel,
        LugarViewModel $lugarViewModel,
        MaterialViewModel $materialViewModel
    ) {
       // $this->fallaViewModel = $fallaViewModel;
        $this->usuarioViewModel = $usuarioViewModel;
        $this->lugarViewModel = $lugarViewModel;
        $this->materialViewModel = $materialViewModel;
    }

    /**
     * Listar todas las fallas
     */
    public function index()
    {
        try {
            $resultado = $this->fallaViewModel->obtenerTodas();
            
            if ($resultado['success']) {
                return view('fallas.index', [
                    'fallas' => $resultado['data'],
                    'mensaje' => $resultado['message'] ?? null
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::index', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al cargar fallas: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para crear nueva falla
     */
    public function create()
    {
        try {
            $usuariosResult = $this->usuarioViewModel->getListData();
            $lugaresResult = $this->lugarViewModel->getListData();
            $materialesResult = $this->materialViewModel->getListData();
            
            return view('fallas.create', [
                'usuarios' => $usuariosResult['data'] ?? [],
                'lugares' => $lugaresResult['data'] ?? [],
                'materiales' => $materialesResult['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::create', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nueva falla en base de datos
     */
    public function store(Request $request)
    {
        try {
            $resultado = $this->fallaViewModel->crear($request->all());
            
            if ($resultado['success']) {
                return redirect()->route('fallas.index')
                    ->with('success', $resultado['message']);
            }

            return back()
                ->withInput()
                ->withErrors($resultado['errors'] ?? [])
                ->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::store', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Error al crear falla: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de una falla específica
     */
    public function show($id)
    {
        try {
            $resultado = $this->fallaViewModel->obtenerPorId($id);
            
            if ($resultado['success']) {
                return view('fallas.show', [
                    'falla' => $resultado['data']
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::show', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al cargar falla: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar falla
     */
    public function edit($id)
    {
        try {
            $resultado = $this->fallaViewModel->obtenerPorId($id);
            $usuariosResult = $this->usuarioViewModel->getListData();
            $lugaresResult = $this->lugarViewModel->getListData();
            $materialesResult = $this->materialViewModel->getListData();
            
            if ($resultado['success']) {
                return view('fallas.edit', [
                    'falla' => $resultado['data'],
                    'usuarios' => $usuariosResult['data'] ?? [],
                    'lugares' => $lugaresResult['data'] ?? [],
                    'materiales' => $materialesResult['data'] ?? []
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::edit', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al cargar formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar falla existente
     */
    public function update(Request $request, $id)
    {
        try {
            $resultado = $this->fallaViewModel->actualizar($id, $request->all());
            
            if ($resultado['success']) {
                return redirect()->route('fallas.index')
                    ->with('success', $resultado['message']);
            }

            return back()
                ->withInput()
                ->withErrors($resultado['errors'] ?? [])
                ->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::update', ['id' => $id, 'error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar falla: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar falla
     */
    public function destroy($id)
    {
        try {
            $resultado = $this->fallaViewModel->eliminar($id);
            
            if ($resultado['success']) {
                return redirect()->route('fallas.index')
                    ->with('success', $resultado['message']);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::destroy', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al eliminar falla: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de una falla
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,en_proceso,resuelta,cancelada'
            ], [
                'estado.required' => 'El estado es obligatorio',
                'estado.in' => 'El estado seleccionado es inválido'
            ]);

            $estado = $request->input('estado');
            $resultado = $this->fallaViewModel->cambiarEstado($id, $estado);
            
            if ($resultado['success']) {
                return back()->with('success', $resultado['message']);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::cambiarEstado', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar estadísticas de fallas
     */
    public function estadisticas()
    {
        try {
            $resultado = $this->fallaViewModel->obtenerEstadisticas();
            
            if ($resultado['success']) {
                return view('fallas.estadisticas', [
                    'estadisticas' => $resultado['data']
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::estadisticas', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al cargar estadísticas: ' . $e->getMessage());
        }
    }

    /**
     * Buscar fallas por término
     */
    public function search(Request $request)
    {
        try {
            $request->validate([
                'q' => 'required|string|min:2|max:100'
            ], [
                'q.required' => 'Ingresa un término de búsqueda',
                'q.min' => 'El término debe tener al menos 2 caracteres'
            ]);

            $termino = $request->get('q');
            $resultado = $this->fallaViewModel->buscar($termino);
            
            if ($resultado['success']) {
                return view('fallas.index', [
                    'fallas' => $resultado['data'],
                    'termino' => $termino
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::search', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al buscar fallas: ' . $e->getMessage());
        }
    }

    /**
     * Filtrar fallas por estado
     */
    public function porEstado($estado)
    {
        try {
            $estadosValidos = ['pendiente', 'en_proceso', 'resuelta', 'cancelada'];
            
            if (!in_array($estado, $estadosValidos)) {
                return back()->with('error', "Estado inválido: {$estado}");
            }

            $resultado = $this->fallaViewModel->obtenerPorEstado($estado);
            
            if ($resultado['success']) {
                return view('fallas.index', [
                    'fallas' => $resultado['data'],
                    'estado_filtro' => $estado
                ]);
            }

            return back()->with('error', $resultado['message']);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::porEstado', ['estado' => $estado, 'error' => $e->getMessage()]);
            return back()->with('error', 'Error al filtrar por estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener fallas por usuario (AJAX)
     */
    public function porUsuario($usuario_id)
    {
        try {
            $resultado = $this->fallaViewModel->obtenerPorUsuario($usuario_id);
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $resultado['data'],
                    'message' => $resultado['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $resultado['message']
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::porUsuario', ['usuario_id' => $usuario_id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener fallas por lugar (AJAX)
     */
    public function porLugar($lugar_id)
    {
        try {
            $resultado = $this->fallaViewModel->obtenerPorLugar($lugar_id);
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $resultado['data'],
                    'message' => $resultado['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $resultado['message']
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::porLugar', ['lugar_id' => $lugar_id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener fallas por prioridad (AJAX)
     */
    public function porPrioridad($prioridad)
    {
        try {
            $prioridadesValidas = ['baja', 'media', 'alta'];
            
            if (!in_array($prioridad, $prioridadesValidas)) {
                return response()->json([
                    'success' => false,
                    'message' => "Prioridad inválida: {$prioridad}"
                ], 400);
            }

            $resultado = $this->fallaViewModel->obtenerPorPrioridad($prioridad);
            
            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $resultado['data'],
                    'message' => $resultado['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $resultado['message']
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error en FallaController::porPrioridad', ['prioridad' => $prioridad, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}