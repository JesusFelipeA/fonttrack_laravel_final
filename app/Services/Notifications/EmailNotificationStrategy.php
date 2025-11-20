<?php

namespace App\Services\Notifications;

use App\Models\Usuarios;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationStrategy implements NotificationStrategyInterface
{
    public function send(Usuarios $user, array $payload): void
    {
        try {
            if (empty($user->correo)) {
                Log::warning('EmailNotificationStrategy: usuario sin correo', ['user_id' => $user->id_usuario ?? null]);
                return;
            }

            Mail::raw($payload['message'] ?? json_encode($payload), function ($m) use ($user, $payload) {
                $m->to($user->correo)->subject($payload['subject'] ?? 'Notificación');
            });
        } catch (\Throwable $e) {
            Log::error('EmailNotificationStrategy error: '.$e->getMessage());
        }
    }
}