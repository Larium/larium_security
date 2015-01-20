<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authentication;

use Larium\Security\User\UserProviderInterface;
use Larium\Security\Storage\StorageInterface;
use Larium\Security\Encoder\PasswordEncoderInterface;
use Larium\Security\Encoder\PlainTextEncoder;
use Larium\Executor\Executor;

class AuthenticationService
{
    const AFTER_AUTHENTICATE = 'after.authenticate';

    protected $user_provider;

    protected $storage;

    protected $encoder;

    protected $executor;

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

        $this->executor = new Executor();
    }

    public function authenticate($username, $password)
    {
        try {

            $this->user = $this->user_provider->getByUsername($username);

            $result = $this->encoder->validate($password, $this->user->getCryptedPassword());

            if (true === $result) {

                $message = new AuthenticationMessage();
                $message->setUser($this->user);

                $this->executor->execute(self::AFTER_AUTHENTICATE, $message);

                $this->storage->setUser($this->user);
                $this->storage->save();
            }

            return $result;

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

    public function isAuthenticated()
    {
        $storage_user = $this->storage->getUser();

        if (null === $storage_user) {
            return false;
        }

        $user = $this->user_provider->getByUsername($storage_user->getUsername());

        $compare = $user->compare($storage_user) && !$this->storage->hasExpired();

        if ($compare) {
            $this->user = $user;
        }

        return $user;
    }

    public function addListener($state, $callback)
    {
        $this->executor->addCommand($state, $callback);
    }
}
