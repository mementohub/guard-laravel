<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use iMemento\JWT\JWT;
use Session;
use Crypt;

class StaticUserProvider implements UserProvider
{

    protected $app_name = 'APP_NAME';

    protected $auth_public_key = 'AUTH_KEY';

    /**
     * The name of the Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * The permissions array.
     *
     * @var array
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

        $this->auth_public_key = base_path(env($this->auth_public_key));
    }

    /**
     * @param string $jwt
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createFromJWT(string $jwt)
    {
        $auth_public_key = JWT::getPublicKey($this->auth_public_key);
        $app_name = env($this->app_name);

        $user_jwt = JWT::decode($jwt, $auth_public_key);

        $data = [
            'id' => $user_jwt->user_id,
            'org_ids' => $user_jwt->org_ids,
            'org_user_ids' => $user_jwt->org_user_ids,
            'token' => $jwt,
            'roles' => $user_jwt->roles->$app_name ?? [],
        ];

        //create our Authenticatable User
        $user = $this->createModel($data);
        $user->createPermissions($this->permissions, $user->roles);

        return $user;
    }

    /**
     * Retrieve a user by their unique identifier.
     * We're not actually retrieving by id, just fetching the user already decoded by our guard
     *
     * @param  mixed $identifier
     * @return UserContract|\Illuminate\Database\Eloquent\Model|null
     */
    public function retrieveById($identifier)
    {
        if(! Session::has('user'))
            return null;


        $user = json_decode(Crypt::decryptString(Session::get('user')), true);

        return $this->createModel($user);
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
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel($data)
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class($data);
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
