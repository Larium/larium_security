<?php

namespace Larium\Security\Storage;

use Larium\Http\Session\SessionInterface;

class SessionStorage implements StorageInterface
{
    protected $session;

    protected $token_key;

    protected $user;

    public function __construct($token_key, SessionInterface $session)
    {
        $this->token_key = $token_key;
        $this->session = $session;
        $this->expire_at = new \Datetime('3 hours');

        $this->getToken();
    }

    public function getToken()
    {
        $data = $this->session->get($this->token_key) ?: array();
        if (empty($data) || count($data) !== 3) {
            return;
        }
        list($token_key, $this->user, $this->expire_at) = $data;
        $this->expire_at = new \Datetime($this->expire_at);
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function save()
    {
        $data = array(
            $this->token_key,
            clone $this->user,
            $this->expire_at->format('Y-m-d H:i:s')
        );

        $this->session->set($this->token_key, $data);
    }

    public function erase()
    {
        return $this->session->delete($this->token_key);
    }
}
