<?php
// app/ViewModels/UsuarioViewModel.php

namespace App\ViewModels;

use App\Repositories\UsuarioRepository;
use App\Repositories\LugarRepository;
use Illuminate\Support\Facades\Auth;

class UsuarioViewModel
{
    protected $repository;
    protected $lugarRepository;

    public function __construct(
        UsuarioRepository $repository,
        LugarRepository $lugarRepository
    ) {
        $this->repository = $repository;
        $this->lugarRepository = $lugarRepository;
    }

    public function getListData(?string $search = null): array
    {
        $usuarios = $this->repository->getPaginated($search);
        $lugares = $this->lugarRepository->getAll();

        return [
            'usuarios' => $usuarios,
            'lugares' => $lugares,
            'totalUsuarios' => $usuarios->total(),
        ];
    }

    public function getViewData(int $id): array
    {
        $usuario = $this->repository->findById($id);
        
        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        $estadisticas = $this->repository->getEstadisticasUsuario($id);

        return [
            'usuario' => $usuario,
            'lugar' => $usuario->lugar,
            'estadisticas' => $estadisticas,
        ];
    }

    public function getCreateData(): array
    {
        return [
            'lugares' => $this->lugarRepository->getAll(),
        ];
    }

    public function getEditData(int $id): array
    {
        $usuario = $this->repository->findById($id);
        
        if (!$usuario) {
            throw new \Exception('Usuario no encontrado');
        }

        return [
            'usuario' => $usuario,
            'lugares' => $this->lugarRepository->getAll(),
        ];
    }

    public function canDeleteUsuario(int $id): bool
    {
        $currentUserId = Auth::user()->id_usuario ?? Auth::user()->id;
        
        if ($id == $currentUserId) {
            return false;
        }

        $estadisticas = $this->repository->getEstadisticasUsuario($id);
        
        return $estadisticas['reportes_creados'] == 0 && $estadisticas['reportes_revisados'] == 0;
    }
}