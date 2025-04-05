<?php

namespace App\Extensions\Centrifuge\Providers;

use App\Extensions\Centrifuge\Centrifuge;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CentrifugeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Centrifuge::class, function (Application $app) {
            return new Centrifuge();
        });
    }
}
