<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

trait AuthenticateResource
{
    public static $AUTH_SITE_KEY = "p9fk2kiEXuu1pfWahrVbkwvOv?osrp4Wujach4oDxxy2NFUjgevz97tv1l1x";
    public static $DIGEST_STRETCHES = 10000;

    public $password;

    public function makeActivationCode()
    {
        $this->activation_code = self::makeToken();
    }

    public function encryptPassword()
    {
        if (empty($this->password)) {
            return;
        }

        if (null == $this->getSalt()) {
            $this->setSalt(static::makeToken());
        }

        $this->setCryptedPassword($this->encrypt($this->password));
    }

    protected function is_authenticated($password)
    {
        return $this->getCryptedPassword() == $this->encrypt($password);
    }

    protected function encrypt($password)
    {
        return static::passwordDigest($password, $this->getSalt());
    }

    public static function authenticate($login_or_email, $password) 
    {
        $u = static::authenticate_resource($login_or_email);
        
        return $u && $u->is_authenticated($password) ? $u : null;
    }

    public static function makeToken() 
    {
        $a = array();
        for( $i=1; $i<=10; $i++ ){
            $a[] = mt_rand();
        }
        $a = implode('-', $a);

        return self::secure_digest(static::$AUTH_SITE_KEY, $a);
    }

    public static function passwordDigest($password, $salt)
    {
        return self::secure_digest($password, $salt);
    }

    protected static function authenticate_resource($login_or_email)
    {
        throw new \BadMethodCallException("Must implement ". get_called_class() . "::".__FUNCTION__);
    }

    public static function findResource($id=null) {
        throw new \BadMethodCallException("Must implement ". get_called_class() . "::".__FUNCTION__);
    }

    private static function secure_digest($digest, $salt) 
    {
        return base64_encode(
            crypt(
                $digest, 
                "$6$"."rounds=".static::$DIGEST_STRETCHES."$" . $salt . "$"
            )
        );
    }
}
