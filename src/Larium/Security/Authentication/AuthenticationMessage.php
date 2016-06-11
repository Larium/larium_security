<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authentication;

use Larium\Executor\Message;

class AuthenticationMessage extends Message
{
    protected $user;

    /**
     * Gets user.
     *
     * @access public
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets user.
     *
     * @param mixed $user the value to set.
     * @access public
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
