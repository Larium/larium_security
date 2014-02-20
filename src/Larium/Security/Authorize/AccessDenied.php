<?php

namespace Larium\Security\Authorize;

require_once 'Exceptions.php';

class AccessDenied extends \OutOfRangeException 
{
    protected $action;
    
    protected $subject;
    
    protected $message;
    
    public    $default_message;

    public function __construct($message=null, $action=null, $subject=null) 
    {
        $this->default_message = "You are not authorized to access this page.";
        $this->message = $message ?: $this->default_message;
        $this->action = $action;
        $this->subject = $subject;
        parent::__construct($message, 401);
    }

    public function __toString() 
    {
        return isset($this->message) ? $this->message : $this->default_message;
    }
}
