# Laravel Helpers: App
###### Part of mphpmaster/laravel-helpers:^3
<small>v1.0.2</small>

## Dependencies:
* php >=8.1 **REQUIRED IN YOUR PROJECT**
* laravel >=9 **REQUIRED IN YOUR PROJECT**
* illuminate/support >=9 _composer will install it automaticly_
* illuminate/filesystem >=9 _composer will install it automaticly_
* laravel/helpers ^1.5 _composer will install it automaticly_

## Installation:
  ```shell
  composer require mphpmaster/laravel-app-helpers
  ```

## Content
- Providers:
    - `MPhpMaster\LaravelAppHelpers\Providers\HelperProvider`.

- Functions:
  - `currentLocale`
  - `currentUrl`
  - `currentUser`
  - `currentUserId`
  - `currentUserName`
  - `currentAuth`
  - `getDefaultGuardName`
  - `getDefaultDiskDriver`
  - `getLocales`
  - `getDefaultLocale`
  - `getModelRelationAttribute`
  - `filenameWithoutExtension`
  - `isAdmin`
  - `isSuperAdmin`
  - `isLocaleAllowed`
  - `isRunningInConsole`
  - `R`
  - `currencyFormat`
  - `currencyFormatEn`
  - `apiResource`
  - `apiResources`
  - `fixPath`
  - `includeSubFiles`
  - `includeAllSubFiles`
  - `includeIfExists`
  - `includeOnceIfExists`


> *Inspired by laravel/helpers.*

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel Helpers: App is open-sourced software licensed under the [MIT license](https://github.com/mPhpMaster/laravel-app-helpers/blob/master/LICENSE).
