<?php

namespace Larium\Security\Authentication;

use Larium\Security\User\User;
use Larium\Security\User\InMemoryUserProvider;
use Larium\Security\Storage\InMemoryStorage;

class TestAuthenticationService extends \PHPUnit_Framework_TestCase
{
    public function testService()
    {
        $provider = $this->getUserProvider();

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
}
