<?php

namespace Larium\Security\Storage;

class InMemoryStorage implements StorageInterface, \Serializable
{
    protected $token;

    protected $expire_at;

    protected $user;

    protected $password;

    protected $tokens = array();

    public function __construct($token)
    {
        $this->token = $token;
        $this->expire_at = new \Datetime('3 hours');
    }

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

    public function expiresAt()
    {
        return $this->expire_at;
    }

    public function hasExpired()
    {
        $interval = $this->expire_at->diff(new \Datetime());

        return $interval->invert !== 1;
    }

    public function save()
    {
        $this->tokens[$this->token] = serialize($this);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function __toString()
    {
        return serialize(array(
            $this->token,
            $this->user,
            $this->password,
            $this->expire_at->format('Y-m-d H:i:s')
        ));
    }

    public function serialize()
    {
        return serialize(array(
            $this->token,
            $this->user,
            $this->password,
            $this->expire_at->format('Y-m-d H:i:s')
        ));
    }

    public function unserialize($data)
    {
        $data = unserialize($data);
        list($this->token, $this->user, $this->password, $this->expire_at) = $data;
        $this->expire_at = new \Datetime($this->expire_at);
    }
}
