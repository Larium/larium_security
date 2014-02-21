<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Encoder;

class BCryptEncoder implements PasswordEncoderInterface
{
    public function encode($password)
    {
        $options = array(
            'cost' => 10
        );

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public function validate($password, $crypted_password)
    {
        return password_verify($password, $crypted_password);
    }
}
