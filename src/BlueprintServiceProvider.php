<?php

namespace Hasanweb\Blueprint;

use Hasanweb\Blueprint\Commands\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {

        $this->commands([
            Blueprint::class,
        ], );

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register() {}
}
