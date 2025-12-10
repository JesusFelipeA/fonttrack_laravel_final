<?php
// app/Http/Controllers/UsuarioController.php

namespace App\Http\Controllers;

use App\ViewModels\UsuarioViewModel;
use App\Services\UsuarioService;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    protected $viewModel;
    protected $service;

    public function __construct(
        UsuarioViewModel $viewModel,
        UsuarioService $service
    ) {
        $this->viewModel = $viewModel;
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->viewModel->getListData($request->get('query'));
        return view('usuarios.index', $data);
    }

    public function store(UsuarioRequest $request)
    {
        $data = $request->validated();
        
        // Agregar foto si existe
        if ($request->hasFile('foto_usuario')) {
            $data['foto_usuario'] = $request->file('foto_usuario');
        }

        $result = $this->service->createUsuario($data);
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

    public function edit(int $id)
    {
        try {
            $data = $this->viewModel->getEditData($id);
            
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

    public function update(UsuarioRequest $request, int $id)
    {
        $data = $request->validated();
        
        // Agregar foto si existe
        if ($request->hasFile('foto_usuario')) {
            $data['foto_usuario'] = $request->file('foto_usuario');
        }

        $result = $this->service->updateUsuario($id, $data);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function destroy(int $id)
    {
        $result = $this->service->deleteUsuario($id);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function verificarPassword(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer',
            'password' => 'required|string',
        ]);

        $result = $this->service->verificarPassword($request->usuario_id, $request->password);
        return response()->json($result);
    }
}