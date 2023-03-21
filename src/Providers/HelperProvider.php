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


        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOr',
            /**
             * Returns first random model, or call the given callback
             *
             * @param \Closure|null         $or_callback when no model found
             * @param \Closure|array|string $columns     to send to model->first()
             *
             * @return \Illuminate\Database\Eloquent\Model|mixed
             */
            function(\Closure $or_callback = null, \Closure|array|string $columns = [ '*' ]) {
                /** @var \Illuminate\Database\Eloquent\Builder $this */
                return $this->inRandomOrder()->firstOr($columns, $or_callback);
            }
        );

        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOrCreate',
            /**
             * Returns first random model, or create new one.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes new model attributes
             *
             * @return \Illuminate\Database\Eloquent\Model|mixed
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Builder $this */
                return $this->randomOr(fn() => $this->create($attributes), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOrNew',
            /**
             * Returns first random model, or make new instance.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes new model attributes
             *
             * @return \Illuminate\Database\Eloquent\Model|mixed
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Builder $this */
                return $this->randomOr(fn() => $this->make($attributes), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOrFactory',
            /**
             * Returns first random model, or new model factory.
             *
             * @param \Closure|array|string $columns to send to model->first()
             *
             * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Factories\Factory<static>
             */
            function(\Closure|array|string $columns = [ '*' ]) {
                /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $this */
                return $this->randomOr(fn() => (is_string($_model = $this->getModel()) ? $_model : get_class($_model))::factory(), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOrCreateFactory',
            /**
             * Returns first random model, or create new model via factory.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes new factory attributes
             *
             * @return \Illuminate\Database\Eloquent\Model
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $this */
                return $this->randomOr(fn() => $this->randomOrFactory()->create($attributes), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Builder::macro(
            'randomOrNewFactory',
            /**
             * Returns first random model, or make new model via factory.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes new factory attributes
             *
             * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Factories\Factory<static>
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Builder $this */
                return $this->randomOr(fn() => $this->randomOrFactory()->make($attributes), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Factories\Factory::macro(
            'randomOr',
            /**
             * Returns first random model, or call the given callback.
             *
             * @param \Closure|null         $or_callback call when no model found
             * @param \Closure|array|string $columns     to send to model->first()
             *
             * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Factories\Factory<static>|mixed
             */
            function(\Closure $or_callback = null, \Closure|array|string $columns = [ '*' ]) {
                /** @var \Illuminate\Database\Eloquent\Factories\Factory $this */
                return $this->modelName()::inRandomOrder()->firstOr($columns, $or_callback);
            }
        );

        \Illuminate\Database\Eloquent\Factories\Factory::macro(
            'randomOrCreate',
            /**
             * Returns first random model, or create new model via factory.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes for new model when create
             *
             * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Factories\Factory<static>|mixed
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Factories\Factory $this */
                return $this->modelName()::randomOr(fn() => $this->create($attributes), $columns);
            }
        );

        \Illuminate\Database\Eloquent\Factories\Factory::macro(
            'randomOrNew',
            /**
             * Returns first random model, or make new model via factory.
             *
             * @param \Closure|array|string $columns    to send to model->first()
             * @param array                 $attributes for new model when create
             *
             * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Factories\Factory<static>|mixed
             */
            function(\Closure|array|string $columns = [ '*' ], array $attributes = []) {
                /** @var \Illuminate\Database\Eloquent\Factories\Factory $this */
                return $this->modelName()::randomOr(fn() => $this->make($attributes), $columns);
            }
        );
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
