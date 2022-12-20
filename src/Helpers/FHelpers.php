<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Route;

if( !function_exists('strReplaceAll') ) {
    /**
     * Replace a given data in string.
     *
     * @param Arrayable<mixed, \Closure|callable|mixed>|array<mixed, \Closure|callable|mixed> $_searchAndReplace
     * @param string                                                                          $_subject
     *
     * @return string
     */
    function strReplaceAll($searchAndReplace, string $subject): string
    {
        if( isArrayable($searchAndReplace) ) {
            /** @var array $searchAndReplace */
            $searchAndReplace = $searchAndReplace->toArray();
        }

        $_subject = $subject;
        foreach( (array) $searchAndReplace as $search => $replace ) {
            $_subject = str_ireplace($search, $replace, $_subject);
        }

        return $_subject;
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
     * @param array  $options
     *
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    function apiResource(string $name, string $controller, array $options = [])
    {
        $only = [ 'index', 'show', 'store', 'update', 'destroy', 'forceDestroy', 'force_destroy', 'forceDelete', 'force_delete', 'restore' ];

        if( isset($options[ 'except' ]) ) {
            $only = array_diff($only, (array) $options[ 'except' ]);
        }

        $sName = str_singular($name);
        if( $only['forceDestroy'] ?? $only['force_destroy'] ?? false )
        {
            Route::delete("{$name}/{{$sName}}/force", [ $controller, 'forceDestroy' ])->name("{$name}.force_delete")->withTrashed();
        }
        if( $only['restore'] ?? false )
        {
            Route::post("{$name}/{{$sName}}/restore", [ $controller, 'restore' ])->name("{$name}.restore")->withTrashed();
        }

        return Route::resource(
            $name,
            $controller,
            array_merge([
                            'only' => $only,
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
