<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$moduleNamespaces = [
    'Basic'       => APP_PATH . '/basic',
    'library'     => APP_PATH . '/library',
    'models'      => APP_PATH . '/models',
    'logic'       => APP_PATH . '/logic',
    'Controllers' => APP_PATH . '/controllers',
];

$loader->registerNamespaces($moduleNamespaces);
$loader->register();