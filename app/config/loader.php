<?php

use Phalcon\Loader;

$loader = new Loader();
$loader->registerNamespaces(
    [
        'App\Services'    => realpath(__DIR__ . '/../services/'),
        'App\Controllers' => realpath(__DIR__ . '/../controllers/'),
        'App\Models'      => realpath(__DIR__ . '/../models/'),
        'App\Exceptions'  => realpath(__DIR__ . '/../exceptions/'),
    ]
);
$loader->register();