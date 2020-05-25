<?php

use Phalcon\Config;
use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../..");
$dotenv->load();

return new Config(
    [
        'database' => [
            'adapter' => 'mysql',
            'host' => $_ENV["MYSQL_HOST"],
            'username' => $_ENV["MYSQL_USERNAME"],
            'password' => $_ENV["MYSQL_PASSWORD"],
            'dbname' => $_ENV["MYSQL_DATABASE"],
            'charset' => 'utf8',
        ],

        'application' => [
            'controllersDir' => "app/controllers/",
            'modelsDir' => "app/models/",
            'baseUri' => "/",
            'logInDb' => true,
            'migrationsDir' => 'app/migrations',
            'migrationsTsBased' => true,
            'exportDataFromTables' => [
                'User',
                'Post',
                'Comment',
                'Notification',
                'Transaction'
            ],
        ],
    ]
);