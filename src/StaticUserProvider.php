<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class StaticUserProvider implements UserProvider
{

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * Create a new database user provider.
     *
     * @param  string  $model
     */
    public function __construct($model)
    {
        $this->model = $model;
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

        $permissions = config('permissions');

        //build the User model
        $model->id = $user->uid;
        $model->agency_id = $user->aid;
        $model->roles = $roles->ua;
        $model->consumer_roles = $roles->cns;

        //create the permissions array
        $ua_permissions = [];
        foreach($model->roles as $role) {
            $ua_permissions = array_merge($ua_permissions, $permissions[$role]);
        }

        //create the consumer permissions array
        $consumer_permissions = [];
        foreach($model->consumer_roles as $role) {
            $consumer_permissions = array_merge($consumer_permissions, $permissions[$role]);
        }

        //intersect the permissions arrays
        $model->permissions = array_intersect($ua_permissions, $consumer_permissions);

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
