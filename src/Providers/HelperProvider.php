<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelAppHelpers\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Class HelperProvider
 *
 * @package MPhpMaster\LaravelAppHelpers\Providers
 */
class HelperProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerMacros();
    }
    
    /**
     * Bootstrap services.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // Builder::defaultStringLength(191);
        // Schema::defaultStringLength(191);

        /**
         * Helpers
         */
        require_once __DIR__ . '/../Helpers/FCheckers.php';
        require_once __DIR__ . '/../Helpers/FFiles.php';
        require_once __DIR__ . '/../Helpers/FGetters.php';
        require_once __DIR__ . '/../Helpers/FHelpers.php';
        require_once __DIR__ . '/../Helpers/FSetters.php';
    }

    /**
     *
     */
    public function registerMacros()
    {
        
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
