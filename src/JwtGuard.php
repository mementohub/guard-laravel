<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Str;
use iMemento\JWT\JWT;

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
        $this->tokenKey = 'X-Memento-Key';
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

        //===========
        //here we will need to do a bit of rewriting - needs to work without user
        //or create a separate guard that doesn't care about user, just about valid jwt
        //===========

        $jwt = new JWT($this->getTokenForRequest());
        $issuer = $jwt->getIssuer();

        $publicKey = openssl_get_publickey(file_get_contents(base_path('keys/' . $issuer)));

        $payload = $jwt->decode($publicKey);

        $this->user = $this->provider->createFromPayload($payload);

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
