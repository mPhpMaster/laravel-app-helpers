<?php /** @noinspection PhpIllegalPsrClassPathInspection */
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

namespace MPhpMaster\LaravelAppHelpers\Providers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Schema\Builder;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Database\Eloquent\Builder::macro('firstOrCreateOrRestore', function(array $attributes = [], array $values = []) {
            /** @var Builder $this */
            $query = in_array(SoftDeletes::class, class_uses_recursive(get_class($this->getModel()))) ?
                $this->withTrashed() :
                $this;

            if( !is_null($instance = $query->where($attributes)->first()) ) {
                if( $instance->trashed() ) {
                    $instance->restore();
                    $instance->refresh();
                }

                return $instance;
            }

            return tap($this->newModelInstance(array_merge($attributes, $values)), function($instance) {
                $instance->save();
            });
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
