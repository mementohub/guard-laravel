# iMemento JWT Guard for Laravel

Takes care of the authorization and sets the roles and permissions for the user and consumer.
Depends on the [iMemento JWT](https://gitlab.com/imemento/composer/packages/jwt) package for JWT related tasks. Decrypting the tokens happens in the JWT package.

## Install

```bash
composer require imemento/guard-laravel
```

Add the service to config/app.php:
```php
iMemento\Guard\Laravel\AuthServiceProvider::class,
```

In config/auth.php add a guard with *jwt* as the driver:
```php
'api' => [
	'driver' => 'jwt',
	'provider' => 'users',
],
```

In config/auth.php add a user provider with *static* as the driver:
```php
'users' => [
	'driver' => 'static',
	'model' => App\User::class,
],
```

## Usage

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
The fields *id*, *agency_id* and *roles* can be null.