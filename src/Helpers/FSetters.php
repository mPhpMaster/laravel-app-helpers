<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if( !function_exists('setCurrentLocale') ) {
    /**
     * @param \Closure|string|null $locale
     *
     * @return bool
     */
    function setCurrentLocale(Closure|string|null $locale = null): bool
    {
        try {
            $session = request()->session();
        } catch(Exception|Error $error) {
            try {
                $session = resolve('session');
                request()->setLaravelSession($session);
            } catch(Exception $exception) {
                $session = optional();
            }
        }
        $language = value($locale);
        $language ??= $session->get('language') ?: getDefaultLocale('en');

        if( $language && isLocaleAllowed($language) ) {
            if( currentLocale() !== $language ) {
                $session->put('language', $language);
                $session->save();

                app()->setLocale($language);
            }

            return true;
        }

        return false;
    }
}
