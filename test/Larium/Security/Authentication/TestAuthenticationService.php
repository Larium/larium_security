<?php

namespace Larium\Security\Authentication;

use Larium\Security\User\User;
use Larium\Security\User\InMemoryUserProvider;
use Larium\Security\Storage\InMemoryStorage;
use Larium\Security\Encoder\BCryptEncoder;

class TestAuthenticationService extends \PHPUnit_Framework_TestCase
{
    public function testService()
    {
        $provider = $this->getEncodedUserProvider();

        $storage = new InMemoryStorage('memory');

        $encoder = new BCryptEncoder();

        $service = new AuthenticationService($provider, $storage, $encoder);

        if ($service->authenticate('admin', 'p@$$')) {
            $storage->setUser($service->getUser());
            $storage->save();
        }

        var_dump($storage->hasExpired());
        print_r($service->getUser());



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
