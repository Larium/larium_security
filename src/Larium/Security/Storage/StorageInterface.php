<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security\Storage;

interface StorageInterface
{
    /**
     *
     * Gets authenticated token
     *
     * @access public
     * @return string
     */
    public function getToken();

    /**
     * Sets expiration date for token
     *
     * @param \Datetime $date
     * @access public
     * @return void
     */
    public function setExpiration(\Datetime $date);

    /**
     * Get expired date time of token
     *
     * @access public
     * @return \Datetime
     */
    public function expiresAt();

    /**
     * Checks if stored token has expired
     *
     * @access public
     * @return boolean
     */
    public function hasExpired();

    /**
     * Get User instance of authenticated user based on token
     *
     * @access public
     * @return UserInterface
     */
    public function getUser();

    public function save();

    public function erase();
}
