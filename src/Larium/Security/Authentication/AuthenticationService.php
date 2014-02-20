<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authentication;

use Larium\Security\User\UserInterface;
use Larium\Security\Storage\StorageInterface;

class AuthenticationService
{
    protected $user;

    protected $storage;


    public function __construct(UserProviderInterface $user_provider, StorageInterface $storage)
    {
        $this->user_provider = $user_provider;
        $this->storage = $storage;
    }

    public function authenticate($username, $password)
    {
        // code...
    }

    /**
     * Gets User instance
     *
     * @access public
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get AuthenticateStorage
     *
     * @access public
     * @return StorageInterface
     */
    public function getAuthenticateStorage()
    {
        return $this->storage;
    }
}
