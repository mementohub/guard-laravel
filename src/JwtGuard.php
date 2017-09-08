<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Str;
use iMemento\JWT\Guard as TokenGuard;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The name of the token key.
     *
     * @var string
     */
    protected $tokenKey;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     */
    public function __construct(UserProvider $provider)
    {
        $this->request = Request::capture();
        $this->provider = $provider;
        $this->tokenKey = 'Bearer';
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if(!is_null($this->user)) {
            return $this->user;
        }

        $token = new TokenGuard($this->getTokenForRequest());
        $user = $token->getUser();
        $roles = $token->getRoles();

        $this->user = $this->provider->createFromPayload($user, $roles);

        return $this->user;
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     * @throws \Exception
     */
    public function getTokenForRequest()
    {
        $header = $this->request->header('Authorization', '');

        if (Str::startsWith($header, $this->tokenKey . ' ')) {
            return Str::substr($header, Str::length($this->tokenKey . ' '));
        }

        throw new \Exception('Missing or invalid Authorization header.');
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return null;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
