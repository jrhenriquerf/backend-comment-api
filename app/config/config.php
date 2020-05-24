<?php

use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => 'mysql',
        'host' => $_ENV["MYSQL_HOST"],
        'username' => $_ENV["MYSQL_USERNAME"],
        'password' => $_ENV["MYSQL_PASSWORD"],
        'dbname' => $_ENV["MYSQL_DATABASE"],
        'charset' => 'utf8',
    ],
    'application' => [
        'logInDb' => true,
        'migrationsDir' => '../migrations',
        'migrationsTsBased' => true,
        'exportDataFromTables' => [
            'User',
            'Post',
            'Comment',
            'Notification',
            'Transaction'
        ],
    ],
]);