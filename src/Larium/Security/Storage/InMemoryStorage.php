<?php

namespace Larium\Security\Storage;

class InMemoryStorage implements StorageInterface
{
    protected $token;

    protected $expire_at;

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setExpiration(\Datetime $date)
    {
        $this->expire_at = $date;
    }

    public function expireAt()
    {
        return $this->expire_at;
    }

    public function hasExpire()
    {
        $interval = $this->expire_at->diff(new \Datetime());

        return $interval->invert !== 1;
    }

    public function save()
    {
        return true;
    }
}
