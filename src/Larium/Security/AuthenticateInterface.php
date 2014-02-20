<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

interface AuthenticateInterface
{
    public function getRoles();

    public function hasRole($role);

    public function getId();

    public function setSalt($salt);

    public function getSalt();

    public function setCryptedPassword($password);

    public function getCryptedPassword();

    public function encryptPassword();
}
