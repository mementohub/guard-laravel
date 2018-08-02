<?php

namespace iMemento\Guard\Laravel;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers Client
 */
class JwtGuardTest extends TestCase
{
    public $provider;
    public $request;

    public function setUp()
    {
        //$this->provider = $this->createMock(StaticUserProvider::class);

        //todo make this work...

        $this->provider = $this->getMockBuilder(StaticUserProvider::class)
            ->setMethods(['createFromJWT', 'retrieveById', 'createModel', 'getModel', 'setModel'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->setMethods(['getMethod', 'retrieveItem', 'getRealMethod', 'all', 'getInputSource', 'get', 'has', 'header'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_user_is_created_correctly()
    {
        $this->request->expects($this->any())
            ->method('header')
            ->with('Authorization')
            ->willReturn('Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhdXRoIiwidXNlcl9pZCI6NjUyMCwicm9sZXMiOnsic3NyX2NvbnNvbGUiOlsiYWRtaW4iXSwic2VydmljZXNfYm9va2luZ3MiOlsidXNlciJdLCJzc3JfaGVsbG8tcm9tYW5pYSI6WyJ1c2VyIiwiYWRtaW4iXX0sIm9yZ19pZHMiOlsxMzQ2XSwib3JnX3VzZXJfaWRzIjpbNjUyMF19.OCxtDlO0gm8okSS4oJozgNUygMuGPDDFDKxrJ-DyDBe-MrOy33ieqBi0xoLp-Md2M8FmdMo-oFLCu4SZsq-A3ZkjyvVeTQJ9VBsX6LuVHaBRgVtC_oUX6O_2GvWZNzTABOA7slwjjsY3fTvYlN_omFHiD4bE3vJ4OZqAG_zzr1c');

        $guard = new JwtGuard($this->provider, $this->request);

        $user = $guard->user();

        dd($user);
    }
    
}