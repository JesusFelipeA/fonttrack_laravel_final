<?php

namespace App\Services\Notifications;

use App\Models\Usuarios;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DatabaseNotificationStrategy implements NotificationStrategyInterface
{
    public function send(Usuarios $user, array $payload): void
    {
        try {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => $payload['type'] ?? 'app.notification',
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id_usuario ?? $user->id ?? null,
                'data' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('DatabaseNotificationStrategy error: '.$e->getMessage());
        }
    }
}