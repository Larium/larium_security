<?php

namespace Larium\Security\Storage;

use Larium\Http\Session\SessionInterface;

class SessionStorage implements StorageInterface
{
    public function __construct(SessionInterface $session)
    {

    }

    public function getToken()
    {
        // code...
    }

    public function setExpiration(\Datetime $date)
    {
        // code...
    }

    public function expiresAt()
    {
        // code...
    }

    public function hasExpired()
    {
        // code...
    }

    public function getUser()
    {
        // code...
    }
}
