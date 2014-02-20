<?php 
namespace Larium\Security\Authorize;

use Exception;

class Error extends Exception {}

class NotImplemented extends Error {}

class ImplementationRemoved extends Error {}

class AuthorizationNotPerformed extends Error {}
