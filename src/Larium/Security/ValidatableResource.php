<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

trait ValidatableResource
{
    use \Larium\Validations\Validate;
    
    public $password_confirmation;

    protected function validations()
    {
        if ($this->is_password_required()) {
            
            $this->validates(
                array(
                    'password', 'password_confirmation'
                ),
                array('Presence' => true)
            );

            $this->validates(
                'password', 
                array(
                    'Length' => array(
                        'in'=>range(4,20)
                    ),
                    'Confirmation' => true
                )
            );
        }

        $this->validates(
            array(
                'email'
            ),
            array(
                'Email' => true
            )
        );
    }

    protected function is_password_required() 
    {
        return $this->crypted_password == '' || !$this->password == '';
    }
}

