<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Encoder;

class PlainTextEncoder implements PasswordEncoderInterface
{
    public function encode($password)
    {
        return $password;
    }

    public function validate($password, $crypted_password)
    {
        return 0 === strcmp($this->encode($password), $crypted_password);
    }
}
