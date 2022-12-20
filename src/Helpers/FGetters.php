<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;

if( !function_exists('currentLocale') ) {
    /**
     * return appLocale
     *
     * @param bool $full
     *
     * @return string
     */
    function currentLocale($full = false): string
    {
        if( $full )
            return (string) app()->getLocale();

        $locale = str_replace('_', '-', app()->getLocale());
        $locale = current(explode("-", $locale));

        return $locale ?: "";
    }
}

if( !function_exists('currentUrl') ) {
    /**
     * Returns current url.
     *
     * @param string|null $key    return as array with key $key and value as url
     * @param bool        $encode use urlencode
     *
     * @return string|array
     */
    function currentUrl(?string $key = null, bool $encode = true)
    {
        $url = request()->url();
        $url = $encode ? urlencode($url) : $url;

        return is_null($key) ? $url : [ $key => $url ];
    }
}

if( !function_exists('currentUser') ) {
    /**
     * @param $default
     * @param $guard
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User|null
     */
    function currentUser($default = null, $guard = null): \Illuminate\Contracts\Auth\Authenticatable|User|null
    {
        return auth($guard ?? 'web')->user() ?? auth()->user() ?? $default;
    }
}

if( !function_exists('currentUserName') ) {
    /**
     * @param $default
     * @param $guard
     *
     * @return mixed
     */
    function currentUserName($default = null, $guard = null)
    {
        return optional(currentUser(null, $guard))->name ?? value($default);
    }
}

if( !function_exists('currentAuth') ) {
    /**
     * @param mixed|null  $default
     * @param string|null $guard
     *
     * @return \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard|\Illuminate\Contracts\Foundation\Application|mixed|null
     */
    function currentAuth($default = null, ?string $guard = '')
    {
        return (is_null($guard) ? auth() : auth($guard ?: 'web')) ?? $default;
    }
}

if( !function_exists('currentUserId') ) {
    /**
     * @param mixed $default
     *
     * @return int|string|null
     */
    function currentUserId($default = null, $guard = null)
    {
        return auth($guard ?? 'web')->id() ?? auth()->id() ?? $default;
    }
}

if( !function_exists('getDefaultDiskDriver') ) {
    /**
     * @param string $default
     *
     * @return string
     */
    function getDefaultDiskDriver($default = 'local'): string
    {
        return config('filesystems.default', ($default ?: 'public'));
    }
}

if( !function_exists('getDefaultGuardName') ) {
    /**
     * @param mixed|null $defaults
     *
     * @return string|null
     */
    function getDefaultGuardName(mixed $defaults = null): string|null
    {
        return config('auth.defaults.guard') ?: value($defaults);
    }
}

if( !function_exists('getModelRelationAttribute') ) {
    /**
     * @param \Illuminate\Database\Eloquent\Concerns\HasRelationships|\App\Models\Model $model
     * @param string|\Closure                                                           $relation_name
     * @param string|\Closure                                                           $attribute
     * @param mixed|null                                                                $default
     *
     * @return mixed
     */
    function getModelRelationAttribute(HasRelationships|Model $model, $relation_name, $attribute, $default = null)
    {
        $relation_name = value($relation_name);
        $attribute = value($attribute);
        if( $model->relationLoaded($relation_name) ) {
            if( $relation = $model->$relation_name ) {
                $default = $relation->$attribute;
            }
        } else {
            $default = $model->$relation_name()->value($attribute);
        }

        return value($default);
    }
}

if( !function_exists('getLocales') ) {
    /**
     * @return array
     */
    function getLocales(bool $withNames = false): array
    {
        $locales = config('app.locales', config('nova.locales', []));

        return $withNames ? array_flip($locales) : array_keys($locales);
    }
}

if( !function_exists('getDefaultLocale') ) {
    /**
     * @param string|\Closure $default
     *
     * @return string|null
     */
    function getDefaultLocale($default = 'en'): string|null
    {
        $default = value($default);

        return config('app.locale', config('app.fallback_locale', $default)) ?: $default;
    }
}
