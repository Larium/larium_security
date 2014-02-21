<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Encoder;

interface PasswordEncoderInterface
{
    public function encode($password);

    public function validate($password, $crypted_password);
}
