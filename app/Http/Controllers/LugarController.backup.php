<?php
// app/Http/Controllers/LugarController.php

namespace App\Http\Controllers;

use App\ViewModels\LugarViewModel;
use App\Services\LugarService;
use App\Http\Requests\LugarRequest;
use Illuminate\Http\Request;

class LugarController extends Controller
{
    protected $viewModel;
    protected $service;

    public function __construct(
        LugarViewModel $viewModel,
        LugarService $service
    ) {
        $this->viewModel = $viewModel;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->viewModel->getListData($request->get('query'));
        return view('lugares.index', $data);
    }

    public function store(LugarRequest $request)
    {
        $result = $this->service->createLugar($request->validated());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function show(int $id)
    {
        try {
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

    public function update(LugarRequest $request, int $id)
    {
        $result = $this->service->updateLugar($id, $request->validated());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function destroy(int $id)
    {
        $result = $this->service->deleteLugar($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function usuarios(int $id)
    {
        try {
            $usuarios = \App\Repositories\LugarRepository::class;
            $usuarios = app($usuarios)->getUsuariosDelLugar($id);
            
            return response()->json([
                'success' => true,
                'usuarios' => $usuarios,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}