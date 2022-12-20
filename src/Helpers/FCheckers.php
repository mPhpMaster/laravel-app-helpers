<?php

use Illuminate\Contracts\Auth\Authenticatable;

if( !function_exists('isAdmin') ) {
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     *
     * @return bool
     * @throws \Throwable
     */
    function isAdmin(Authenticatable|null $user = null): bool
    {
        $user ??= currentUser();
        $userClass = is_string($user) ? $user : get_class($user);
        throw_if($user && !method_exists($user, 'isAdmin'), "Method [isAdmin] missing in " . ($userClass));

        return $user?->isAdmin();
    }
}

if( !function_exists('isSuperAdmin') ) {
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     *
     * @return bool
     * @throws \Throwable
     */
    function isSuperAdmin(Authenticatable|null $user = null): bool
    {
        $user ??= currentUser();
        $userClass = is_string($user) ? $user : get_class($user);
        throw_if($user && !method_exists($user, 'isSuperAdmin'), "Method [isSuperAdmin] missing in " . ($userClass));

        return $user?->isSuperAdmin();
    }
}

if( !function_exists('isLocaleAllowed') ) {
    /**
     * @param \Closure|string $locale
     *
     * @return bool
     */
    function isLocaleAllowed(Closure|string $locale): bool
    {
        return array_key_exists(value($locale), array_flip(getLocales(true)));
    }
}

if( !function_exists('isRunningInConsole') ) {
    /**
     * @return bool
     */
    function isRunningInConsole(): bool
    {
        static $runningInConsole = null;

        if( isset($_ENV[ 'APP_RUNNING_IN_CONSOLE' ]) || isset($_SERVER[ 'APP_RUNNING_IN_CONSOLE' ]) ) {
            return ($runningInConsole = $_ENV[ 'APP_RUNNING_IN_CONSOLE' ]) ||
                ($runningInConsole = $_SERVER[ 'APP_RUNNING_IN_CONSOLE' ]) === 'true';
        }

        return $runningInConsole = $runningInConsole ?: (
            \Illuminate\Support\Env::get('APP_RUNNING_IN_CONSOLE') ??
            (\PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg' || in_array(php_sapi_name(), [ 'cli', 'phpdb' ]))
        );
    }
}
