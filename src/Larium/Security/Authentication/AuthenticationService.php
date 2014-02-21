<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authentication;

use Larium\Security\User\UserProviderInterface;
use Larium\Security\Storage\StorageInterface;
use Larium\Security\Encoder\PasswordEncoderInterface;
use Larium\Security\Encoder\PlainTextEncoder;

class AuthenticationService
{
    protected $user_provider;

    protected $storage;

    protected $encoder;

    /**
     *
     * @param UserProviderInterface     $user_provider
     * @param StorageInterface          $storage
     * @param PasswordEncoderInterface  $encoder
     * @access public
     * @return void
     */
    public function __construct(
        UserProviderInterface $user_provider,
        StorageInterface $storage,
        PasswordEncoderInterface $encoder = null
    ) {

        $this->user_provider = $user_provider;
        $this->storage = $storage;

        $encoder = $encoder ?: new PlainTextEncoder();

        $this->encoder = $encoder;
    }

    public function authenticate($username, $password)
    {
        try {

            $this->user = $this->user_provider->getByUsername($username);

            return $this->encoder->validate($password, $this->user->getCryptedPassword());

        } catch (UserNotFoundException $e) {
            throw $e;
        }
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
