<?php

namespace App\Services;

use App\Models\Usuarios;

class DashboardManager
{
    public function getDashboardFor(Usuarios $usuario): array
    {
        if ($this->isAdmin($usuario)) {
            return $this->getAdminDashboard($usuario);
        }

        return $this->getUsuariosDashboard($usuario);
    }

    protected function isAdmin(Usuarios $usuario): bool
    {
        if (method_exists($usuario, 'hasRole')) {
            return $usuario->hasRole('admin');
        }

        if (property_exists($usuario, 'is_admin')) {
            return (bool) $usuario->is_admin;
        }

        return (($usuario->rol ?? '') === 'admin');
    }

    protected function getAdminDashboard(Usuarios $usuario): array
    {
        return [
            'title' => 'Panel de Administración',
            'widgets' => [
                'stats' => [
                    'Usuarioss' => \App\Models\Usuarios::count(),
                ],
                'recentUsuarioss' => \App\Models\Usuarios::latest()->take(5)->get(),
            ],
        ];
    }

    protected function getUsuariosDashboard(Usuarios $Usuarios): array
    {
        return [
            'title' => 'Mi Panel',
            'widgets' => [
                'welcome' => "Hola {$Usuarios->name}"
            ],
        ];
    }
}