<?php
// app/ViewModels/MaterialViewModel.php

namespace App\ViewModels;

use App\Repositories\MaterialRepository;
use Illuminate\Support\Facades\Auth;

class MaterialViewModel
{
    protected $repository;

    public function __construct(MaterialRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getListData(?string $search = null): array
    {
        $user = Auth::user();
        $idLugar = $user->tipo_usuario == 1 ? null : $user->id_lugar;

        $materiales = $this->repository->getFiltered($idLugar, $search);

        // Obtener lugares para el select
        $lugares = \App\Models\Lugar::all();

        return [
            'materiales' => $materiales,
            'lugares' => $lugares,
            'user' => $user,
            'totalMateriales' => $materiales->total(),
            'isAdmin' => $user->tipo_usuario == 1,
            'lugarNombre' => $user->lugar->nombre ?? 'Sin lugar asignado',
        ];
    }

    public function getViewData(int $id): array
    {
        $material = $this->repository->findById($id);
        
        if (!$material) {
            throw new \Exception('Material no encontrado');
        }

        return [
            'material' => $material,
            'lugar' => $material->lugar,
        ];
    }

    public function canAccessMaterial(int $materialId): bool
    {
        $user = Auth::user();
        
        if ($user->tipo_usuario == 1) {
            return true;
        }

        $material = $this->repository->findById($materialId);
        return $material && $material->id_lugar == $user->id_lugar;
    }
}