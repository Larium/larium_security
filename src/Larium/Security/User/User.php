<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\User;

class User implements UserInterface
{
    /**
     * @var string
     * @access protected
     */
    protected $username;

    /**
     * @var string
     * @access protected
     */
    protected $crypted_password;

    /**
     * @var array
     * @access protected
     */
    protected $roles = array('ROLE_USER');

    public function __construct($username, $crypted_password, array $roles = array())
    {
        $this->username = $username;
        $this->crypted_password = $crypted_password;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getCryptedPassword()
    {
        return $this->crypted_password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function compare(UserInterface $user)
    {
        return 0 === strcmp($this->crypted_password, $user->getCryptedPassword())
            && 0 === strcmp($this->username, $user->getUsername());
    }
}
