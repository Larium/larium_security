<?php

namespace Larium\Security\Authentication;

use Larium\Security\User\User;
use Larium\Security\User\InMemoryUserProvider;
use Larium\Security\Storage\InMemoryStorage;
use Larium\Security\Storage\SessionStorage;
use Larium\Security\Encoder\BCryptEncoder;

use Larium\Http\Session\Session;
use Larium\Http\Session\Handler\FileSessionHandler;

class TestAuthenticationService extends \PHPUnit_Framework_TestCase
{
    public function testService()
    {
        $provider = $this->getEncodedUserProvider();
        $storage = new InMemoryStorage('memory');
        $encoder = new BCryptEncoder();

        $service = new AuthenticationService($provider, $storage, $encoder);

        if (!$service->isAuthenticated()) {
            $service->authenticate('admin', 'p@$$');
        }

        $this->assertEquals('admin', $service->getUser()->getUsername());
    }

    public function testServiceWithSessionStorage()
    {
        $provider = $this->getEncodedUserProvider();
        $session_handler = new FileSessionHandler(__DIR__ . '/../../../tmp');
        $session = new Session($session_handler);
        $storage = new SessionStorage('sess_auth', $session);
        $encoder = new BCryptEncoder();

        $service = new AuthenticationService($provider, $storage, $encoder);

        if (!$service->isAuthenticated()) {
            $service->authenticate('admin', 'p@$$');
        }

        $this->assertEquals('admin', $service->getUser()->getUsername());
    }

    private function getUserProvider()
    {
        $users = array(
            array(
                'username' => 'andreas',
                'password' => 's3cr3t',
                'roles'    => array('ROLE_USER')
            ),
            array(
                'username' => 'admin',
                'password' => 'p@$$',
                'roles'    => array('ROLE_ADMIN')
            ),
        );

        return new InMemoryUserProvider($users);
    }

    private function getEncodedUserProvider()
    {
        $encoder = new BCryptEncoder();

        $users = array(
            array(
                'username' => 'andreas',
                'password' => $encoder->encode('s3cr3t'),
                'roles'    => array('ROLE_USER')
            ),
            array(
                'username' => 'admin',
                'password' => $encoder->encode('p@$$'),
                'roles'    => array('ROLE_ADMIN')
            ),
        );

        return new InMemoryUserProvider($users);
    }
}
