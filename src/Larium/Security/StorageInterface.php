<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\Security;

interface Storage
{
    public function setAutenticatedResource(AuthenticatableInterface $resource);
    
    public function getAutenticatedResource();
}
