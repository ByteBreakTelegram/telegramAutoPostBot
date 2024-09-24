<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TelegramBot\Api\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация BotApi как синглтон
        $this->app->singleton(Client::class, function ($app) {
            return new Client(config('telegram.client.token'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
