<?php

namespace App\Services\Notifications;

use App\Models\Usuarios;
use Illuminate\Support\Facades\Log;

class PushNotificationStrategy implements NotificationStrategyInterface
{
    public function send(Usuarios $user, array $payload): void
    {
        // Integración con FCM/APNs aquí. Por ahora solo log.
        Log::info('PushNotificationStrategy send', [
            'user_id' => $user->id_usuario ?? $user->id ?? null,
            'payload' => $payload,
        ]);
    }
}