# iMemento JWT Guard for Laravel
[![Build Status](https://github.com/mementohub/guard-laravel/workflows/Testing/badge.svg)](https://github.com/mementohub/guard-laravel/actions)
[![Latest Stable Version](https://img.shields.io/packagist/v/imemento/guard-laravel)](https://packagist.org/packages/imemento/guard-laravel)
[![License](https://img.shields.io/packagist/l/imemento/guard-laravel)](https://packagist.org/packages/imemento/guard-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/imemento/guard-laravel)](https://packagist.org/packages/imemento/guard-laravel)

Takes care of the authorization and sets the roles and permissions for the user and consumer.
Depends on the [iMemento JWT](https://gitlab.com/imemento/composer/packages/jwt) package for JWT related tasks. Decrypting the tokens happens in the JWT package.

## Install

```bash
composer require imemento/guard-laravel
```

The package uses Service Discovery. Still, if necessary, you can add the service to `config/app.php`:
```php
iMemento\Guard\Laravel\AuthServiceProvider::class,
```

In `config/auth.php` add a guard with *jwt* as the driver:
```php
'api' => [
	'driver' 	=> 'jwt',
	'provider' 	=> 'users',
],
```

In `config/auth.php` add a user provider with *static* as the driver.
The model needs to be an instance of `iMemento\SDK\Auth\User::class` or an extension of it.
```php
'users' => [
	'driver' 	=> 'static',
	'model' 	=> iMemento\SDK\Auth\User::class,
],
```

## Dependencies

Since this package handles multiple operations in order to achieve the desired results, the following
`.env` variables should be properly defined:

```bash
AUTH_KEY=
```


## Usage

To use the JWT Guard for all the routes in your `routes/api.php` file you just need to add it
to the `api` middleware group in `app/Http/Kernel.php`.
```php
'api' => [
	'throttle:60,1',
	'bindings',
	'auth:api', #this
],
```

If your API exposes public endpoints, the ones that should be guarded by the JWT Guard should be
specifically grouped:
```php
	Route::group(['middleware' => 'auth:api'], function ()) {
		//...
	}
```

## Authenticated user

Once the Guard has been applied, the app will have access to an authenticated user through `auth()->user()`.

The following fields are added to the current user and can be used in the application's policies.
```json
{
  "id": 13,
  "agency_id": 2,
  "roles": ["admin"],
  "consumer_roles": ["user"],
  "permissions": ["read","write"]
}
```
The fields *id*, *agency_id* can be null, *roles* can be empty.
