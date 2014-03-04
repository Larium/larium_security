<?php

namespace Larium\Security\Storage;

class InMemoryStorage implements StorageInterface, \Serializable
{
    protected $token_key;

    protected $expire_at;

    protected $user;

    protected $tokens = array();

    public function __construct($token_key)
    {
        if (empty($token_key)) {
            throw new \InvalidArgumentException('Token key should not be empty.');
        }

        $this->token_key = $token_key;
        $this->expire_at = new \Datetime('3 hours');
    }

    public function getToken()
    {
        return array_key_exists($this->token_key, $this->tokens)
            ? $this->tokens[$this->token_key]
            : null;
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

        $expired = $interval->invert !== 1;

        if ($expired) {
            unset($this->tokens[$this->token_key]);
        }

        return $expired;
    }

    public function save()
    {
        $this->tokens[$this->token_key] = serialize($this);
    }

    public function erase()
    {
        unset($this->tokens[$this->token_key]);
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
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
            $this->token_key,
            $this->user,
            $this->expire_at->format('Y-m-d H:i:s')
        ));
    }

    public function unserialize($data)
    {
        $data = unserialize($data);
        list($this->token_key, $this->user, $this->expire_at) = $data;
        $this->expire_at = new \Datetime($this->expire_at);
    }
}
