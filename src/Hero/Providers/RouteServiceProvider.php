<?php

namespace Hero\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app
            ->make('router')
            ->group(
                ['namespace' => 'Hero\Http\Controllers'],
                function ($router) {
                    require $this->app->appPath('Http/routes.php');
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }
}
