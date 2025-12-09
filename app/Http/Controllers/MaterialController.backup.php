
<?php
// app/Http/Controllers/MaterialController.php

namespace App\Http\Controllers;


use App\Models\Material;
use App\Models\Lugar;
use App\Models\Usuarios;
use App\Models\Vehiculo;
use App\ViewModels\MaterialViewModel;
use App\Services\MaterialService;
use App\Http\Requests\MaterialRequest;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    protected $viewModel;
    protected $service;

    public function __construct(
        MaterialViewModel $viewModel,
        MaterialService $service
    ) {
        $this->viewModel = $viewModel;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::all();
        $materiales = Material::all();
        
        $data = $this->viewModel->getListData($request->get('query'));
        
        return view('index_materiales', compact('materiales', 'lugares', 'vehiculos'));
    }

    public function store(MaterialRequest $request)
    {
        $request->validate([
            'clave_material' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'generico' => 'nullable|string|max:255',
            'clasificacion' => 'nullable|string|max:255',
            'existencia' => 'required|integer|min:0',
            'costo_promedio' => 'required|numeric|min:0',
            'id_lugar' => 'required|integer|exists:tb_lugares,id_lugar',
        ]);

        $material = Material::create($request->all());
        return response()->json(['message' => 'Material agregado correctamente', 'data' => $material], 201);
    }

    public function show(int $id)
    {
        try {
            if (!$this->viewModel->canAccessMaterial($id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para ver este material',
                ], 403);
            }

            $data = $this->viewModel->getViewData($id);
            
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(MaterialRequest $request, int $id)
    {
        $result = $this->service->updateMaterial($id, $request->validated());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function destroy(int $id)
    {
        $result = $this->service->deleteMaterial($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function aumentar(Request $request, int $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $result = $this->service->aumentarExistencia($id, $request->cantidad);
        return response()->json($result, $result['success'] ? 200 : 400);
    }
    
}