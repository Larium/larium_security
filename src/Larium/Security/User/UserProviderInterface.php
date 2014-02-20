<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\User;

class UserProviderInterface
{
    /**
     * Gets a User instance by username
     *
     * @param string $username
     * @access public
     * @return UserInterface
     */
    public function getByUsername($username);
}
