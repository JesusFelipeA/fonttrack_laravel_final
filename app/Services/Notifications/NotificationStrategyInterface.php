<?php

namespace App\Services\Notifications;

use App\Models\Usuarios;

interface NotificationStrategyInterface
{
    /**
     * Envía la notificación al usuario con el payload dado.
     *
     * @param Usuarios $user
     * @param array $payload
     * @return void
     */
    public function send(Usuarios $user, array $payload): void;
}