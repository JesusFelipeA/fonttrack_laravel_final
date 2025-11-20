<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NotificationManager;
use App\Services\Notifications\EmailNotificationStrategy;
use App\Services\Notifications\DatabaseNotificationStrategy;
use App\Services\Notifications\PushNotificationStrategy;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(EmailNotificationStrategy::class, fn($app) => new EmailNotificationStrategy());
        $this->app->bind(DatabaseNotificationStrategy::class, fn($app) => new DatabaseNotificationStrategy());
        $this->app->bind(PushNotificationStrategy::class, fn($app) => new PushNotificationStrategy());

        $this->app->singleton(NotificationManager::class, function ($app) {
            return new NotificationManager([
                'email' => $app->make(EmailNotificationStrategy::class),
                'db'    => $app->make(DatabaseNotificationStrategy::class),
                'push'  => $app->make(PushNotificationStrategy::class),
            ]);
        });
    }
}