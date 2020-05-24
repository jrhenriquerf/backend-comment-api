<?php

use Phalcon\Loader;
use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../..");
$dotenv->load();

$loader = new Loader();
$loader->registerNamespaces(
    [
        'MyApp\Models' => __DIR__ . '/../models/',
    ]
);
$loader->register();