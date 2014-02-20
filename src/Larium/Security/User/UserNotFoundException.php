<?php

namespace Larium\Security\User;

class UserNotFoundException extends \RuntimeException
{
    public function __construct($message = "User could not be found", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
