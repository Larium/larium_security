<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\User;

interface UserInterface
{
    /**
     * Gets user name.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Gets encoded password.
     *
     * @return string
     */
    public function getCryptedPassword();

    /**
     * Gets the roles assign to the User
     *
     * @return array
     */
    public function getRoles();
}
