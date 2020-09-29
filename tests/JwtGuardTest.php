<?php

namespace iMemento\Guard\Laravel\Tests;

use ReflectionClass;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Illuminate\Auth\GenericUser;
use iMemento\Guard\Laravel\JwtGuard;
use iMemento\Guard\Laravel\StaticUserProvider;

/**
 * @covers Client
 */
class JwtGuardTest extends TestCase
{
    public $provider;
    public $request;
    public $user;

    public function setUp(): void
    {
        $this->provider = $this->getMockBuilder(StaticUserProvider::class)
            ->setConstructorArgs([new GenericUser([]), ['admin' => 'test']])
            ->setMethods([/*'createFromJWT', 'retrieveById',*/'createModel', /*'getModel', 'setModel',*/ 'getTokenForRequest'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->setMethods(['getMethod', 'retrieveItem', 'getRealMethod', 'all', 'getInputSource', 'get', 'has', 'header'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->user = $this->getMockBuilder(GenericUser::class)
            ->setMethods(['createPermissions', 'getPermissions', 'getRoles'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_user_is_created_correctly()
    {
        $permissions = [
            'admin' => [
                'perm1',
                'perm2',
            ]
        ];
        $roles = ['admin'];

        $this->setProtectedProperty($this->provider, 'auth_public_key', __DIR__ . '/auth');
        $this->setProtectedProperty($this->provider, 'model', 'Illuminate\Auth\GenericUser');

        $user_magic_mock = $this->getMockForTrait('iMemento\SDK\Auth\UserMagicTrait');

        $this->user->expects($this->any())
            ->method('createPermissions')
            ->willReturn($user_magic_mock->createPermissions($permissions, $roles));

        $this->user->expects($this->any())
            ->method('getPermissions')
            ->willReturn($user_magic_mock->getPermissions());

        $this->provider->expects($this->any())
            ->method('createModel')
            ->willReturn($this->user);

        $this->provider->expects($this->any())
            ->method('getTokenForRequest')
            ->willReturn('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhdXRoIiwidXNlcl9pZCI6NjUyMCwicm9sZXMiOnsic3NyX2NvbnNvbGUiOlsiYWRtaW4iXSwic2VydmljZXNfYm9va2luZ3MiOlsidXNlciJdLCJzc3JfaGVsbG8tcm9tYW5pYSI6WyJ1c2VyIiwiYWRtaW4iXX0sIm9yZ19pZHMiOlsxMzQ2XSwib3JnX3VzZXJfaWRzIjpbNjUyMF19.OCxtDlO0gm8okSS4oJozgNUygMuGPDDFDKxrJ-DyDBe-MrOy33ieqBi0xoLp-Md2M8FmdMo-oFLCu4SZsq-A3ZkjyvVeTQJ9VBsX6LuVHaBRgVtC_oUX6O_2GvWZNzTABOA7slwjjsY3fTvYlN_omFHiD4bE3vJ4OZqAG_zzr1c');

        $this->request->expects($this->any())
            ->method('header')
            ->with('Authorization')
            ->willReturn('Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJhdXRoIiwidXNlcl9pZCI6NjUyMCwicm9sZXMiOnsic3NyX2NvbnNvbGUiOlsiYWRtaW4iXSwic2VydmljZXNfYm9va2luZ3MiOlsidXNlciJdLCJzc3JfaGVsbG8tcm9tYW5pYSI6WyJ1c2VyIiwiYWRtaW4iXX0sIm9yZ19pZHMiOlsxMzQ2XSwib3JnX3VzZXJfaWRzIjpbNjUyMF19.OCxtDlO0gm8okSS4oJozgNUygMuGPDDFDKxrJ-DyDBe-MrOy33ieqBi0xoLp-Md2M8FmdMo-oFLCu4SZsq-A3ZkjyvVeTQJ9VBsX6LuVHaBRgVtC_oUX6O_2GvWZNzTABOA7slwjjsY3fTvYlN_omFHiD4bE3vJ4OZqAG_zzr1c');

        $guard = new JwtGuard($this->provider, $this->request);

        $user = $guard->user();

        $computed_perms = $user->getPermissions();

        $this->assertEquals($computed_perms, $permissions['admin']);
    }

    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }
}
