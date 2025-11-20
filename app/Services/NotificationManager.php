<?php

namespace App\Services;

use App\Models\Usuarios;
use App\Services\Notifications\NotificationStrategyInterface;
use Illuminate\Support\Facades\Log;

class NotificationManager
{
    protected array $strategies;

    /**
     * Espera array asociativo: ['email'=>strategy, 'db'=>strategy, 'push'=>strategy]
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    public function send(Usuarios $user, ?string $channel, array $payload): void
    {
        $key = $this->resolveChannel($user, $channel);

        if (isset($this->strategies[$key]) && $this->strategies[$key] instanceof NotificationStrategyInterface) {
            $this->strategies[$key]->send($user, $payload);
            return;
        }

        Log::warning("NotificationManager: estrategia no encontrada para canal {$key}", ['user_id' => $user->id_usuario ?? $user->id ?? null]);
    }

    protected function resolveChannel(Usuarios $user, ?string $channel): string
    {
        if ($channel) {
            return $channel;
        }

        if (!empty($user->pref_notificacion)) {
            return $user->pref_notificacion;
        }

        return 'db';
    }
}