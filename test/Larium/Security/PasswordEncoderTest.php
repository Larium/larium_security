<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

class PasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testPasswordValid()
    {
        $password = 's3cr3t';
        $salt = '5233457e0b1a8';

        $encoder = new PasswordEncoder();

        $encoded = $encoder->encode($password, $salt);

        $this->assertTrue($encoder->isPasswordValid($encoded, $password, $salt));

        $this->assertFalse($encoder->isPasswordValid($encoded, 'sacr3t', $salt));
    }
}
