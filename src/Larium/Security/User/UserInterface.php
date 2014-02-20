<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Authentication;

class UserInterface
{
    /**
     * Gets user name.
     *
     * @access public
     * @return string
     */
    public function getUsername();

    /**
     * Gets encoded password.
     *
     * @access public
     * @return string
     */
    public function getCryptedPassword();

    /**
     * Gets the roles assign to the User
     *
     * @access public
     * @return array
     */
    public function getRoles();
}
