<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use iMemento\JWT\Guard;

class StaticUserProvider implements UserProvider
{

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * The permissions array.
     *
     * @var string
     */
    protected $permissions;

    /**
     * Create a new database user provider.
     *
     * @param  string  $model
     * @param  string  $permissions
     */
    public function __construct($model, $permissions)
    {
        $this->model = $model;
        $this->permissions = $permissions;
    }

    /**
     *
     * @param $user
     * @param $roles
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createFromPayload($user, $roles)
    {
        $model = $this->createModel();

        $model = Guard::createUserModel($model, $user, $roles, $this->permissions);

        return $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     * We're not actually retrieving, but creating the user
     *
     * @param  mixed $identifier
     * @return UserContract|\Illuminate\Database\Eloquent\Model|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();
        $model->id = $identifier;

        return $model;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        return null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        return null;
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Gets the name of the Eloquent user model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}
