<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

//Dependecy
require_once 'larium_http/autoload.php';

require_once 'SplClassLoader.php';

$loader = new SplClassLoader('Larium', __DIR__ . '/src');
$loader->register();
