<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

class PasswordEncoder
{
    protected $stretches;

    public function __construct($stretches = 10000)
    {
        $this->stretches = $stretches;
    }

    /**
     * Encodes plain text with SHA-512 hash type.
     *
     * Returns a bse^4 encoded string from encrypted password.
     *
     * @param string $password
     * @param string $salt
     * @access public
     * @return string
     */
    public function encode($password, $salt)
    {
        return base64_encode(
            crypt(
                $password,
                "$6$"."rounds=".$this->stretches."$" . $salt . "$"
            )
        );
    }

    /**
     * Checks if given encoded password is valid.
     *
     * @param string $encoded
     * @param string $password
     * @param string $salt
     * @access public
     * @return boolean
     */
    public function isPasswordValid($encoded, $password, $salt)
    {
        return 0 === strcmp($encoded, $this->encode($password, $salt));
    }
}
