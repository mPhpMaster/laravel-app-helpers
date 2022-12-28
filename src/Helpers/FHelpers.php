<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

use Illuminate\Support\Facades\Route;

if( !function_exists('replaceAll') ) {
    /**
     * Replace a given data in string.
     *
     * @param iterable|\Closure $searchAndReplace [ searchFor => replaceWith ]
     * @param string|\Closure   $subject
     *
     * @return string
     */
    function replaceAll(iterable|Closure $searchAndReplace, string|Closure $subject): string
    {
        $subject = (string) value($subject);
        $searchAndReplace = value($searchAndReplace);

        foreach( $searchAndReplace as $search => $replace ) {
            $subject = str_ireplace($search, $replace, $subject);
        }

        return $subject;
    }
}

if( !function_exists('R') ) {
    /**
     * Returns the rounded value of val to specified precision (number of digits after the decimal point).
     * precision can also be negative or zero (default).
     * Note: PHP doesn't handle strings like "12,300.2" correctly by default. See converting from strings.
     *
     * @link https://php.net/manual/en/function.round.php
     *
     * @param float|int $number    <p>
     *                             The value to round
     *                             </p>
     * @param int       $precision [optional] <p>
     *                             The optional number of decimal digits to round to.
     *                             </p>
     * @param int       $mode      [optional] <p>
     *                             One of PHP_ROUND_HALF_UP,
     *                             PHP_ROUND_HALF_DOWN,
     *                             PHP_ROUND_HALF_EVEN, or
     *                             PHP_ROUND_HALF_ODD.
     *                             </p>
     *
     * @return float The rounded value
     */
    function R(float|int $number, int $precision = 2, int $mode = PHP_ROUND_HALF_UP): float
    {
        return round($number, $precision, $mode);
    }
}

if( !function_exists('currencyFormat') ) {
    /**
     * @param float|int|\Closure|string $value
     * @param array                     $options [currency = null, locale = null, digits = 2]
     *
     * @return string
     */
    function currencyFormat(
        float|int|Closure|string $value,
        array $options = [
            'currency' => null,
            'locale' => null,
            'digits' => 2,
        ]
    ): string {
        $locale = data_get($options, 'locale') ?: currentLocale();
        $currency = data_get($options, 'currency') ?: config('app.currency', config('nova.currency', 'USD'));
        $digits = data_get($options, 'digits') ?: 2;

        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formatter->setAttribute($formatter::FRACTION_DIGITS, $digits);

        return trim($formatter->formatCurrency(value($value), $currency));
    }
}

if( !function_exists('currencyFormatEn') ) {
    /**
     * alias for currencyFormat, default locale is en
     *
     * @param float|int|\Closure|string $value
     * @param array                     $options [currency = null, locale = null, digits = 2]
     *
     * @return string
     */
    function currencyFormatEn(
        float|int|Closure|string $value,
        array $options = [
            'currency' => null,
            'locale' => 'en',
            'digits' => 2,
        ]
    ): string {
        return currencyFormat($value, $options);
    }
}

if( !function_exists('apiResource') ) {
    /**
     * Route an API resource to a controller.
     *
     * @param string $name
     * @param string $controller
     * @param array  $options [except=>,only=>]
     *
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    function apiResource(string $name, string $controller, array $options = [])
    {
        $only = [ 'index', 'show', 'store', 'update', 'destroy', 'forceDestroy', 'force_destroy', 'forceDelete', 'force_delete', 'restore' ];

        if( isset($options[ 'except' ]) ) {
            $options[ 'except' ] = array_map(fn($value) => ends_with(snake_case($value), "_delete") ? str_ireplace("_delete", "_destroy", snake_case($value)) : $value, (array) $options[ 'except' ]);
            if( in_array('force_destroy', $options[ 'except' ]) ) {
                $options[ 'except' ][] = 'force_delete';
            }

            $only = array_diff($only, $options[ 'except' ]);
            $only = array_diff($only, array_map('snake_case', $options[ 'except' ]));
            $only = array_diff($only, array_map('camel_case', $options[ 'except' ]));
        }
        $only = array_combine($only, $only);

        $sName = str_singular($name);
        if( $only[ 'forceDestroy' ] ?? $only[ 'force_destroy' ] ?? false ) {
            Route::delete("{$name}/{{$sName}}/force", [ $controller, 'forceDestroy' ])->name("{$name}.force_delete")->withTrashed();
        }
        if( $only[ 'restore' ] ?? false ) {
            Route::post("{$name}/{{$sName}}/restore", [ $controller, 'restore' ])->name("{$name}.restore")->withTrashed();
        }

        return Route::resource(
            $name,
            $controller,
            array_merge([
                            'only' => array_keys($only),
                        ], $options)
        );
    }
}

if( !function_exists('apiResources') ) {
    /**
     * Register an array of API resource controllers.
     *
     * @param array $resources
     * @param array $options
     *
     * @return void
     */
    function apiResources(array $resources, array $options = []): void
    {
        foreach( $resources as $name => $controller ) {
            apiResource($name, $controller, $options);
        }
    }
}
