<?php
// app/ViewModels/LugarViewModel.php

namespace App\ViewModels;

use App\Repositories\LugarRepository;

class LugarViewModel
{
    protected $repository;

    public function __construct(LugarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getListData(?string $search = null): array
    {
        $lugares = $this->repository->getPaginated($search);

        return [
            'lugares' => $lugares,
            'totalLugares' => $lugares->total(),
        ];
    }

    public function getViewData(int $id): array
    {
        $lugar = $this->repository->findById($id);
        
        if (!$lugar) {
            throw new \Exception('Lugar no encontrado');
        }

        $estadisticas = $this->repository->getEstadisticas($id);
        $usuarios = $this->repository->getUsuariosDelLugar($id);

        return [
            'lugar' => $lugar,
            'estadisticas' => $estadisticas,
            'usuarios' => $usuarios,
        ];
    }

    public function getLugaresParaSelect(): array
    {
        $lugares = $this->repository->getLugaresActivos();
        
        return $lugares->map(function($lugar) {
            return [
                'id' => $lugar->id_lugar,
                'nombre' => $lugar->nombre,
            ];
        })->toArray();
    }
}