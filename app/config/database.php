<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

$container = new FactoryDefault();
$container->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => $_ENV["MYSQL_HOST"],
                'username' => $_ENV["MYSQL_USERNAME"],
                'password' => $_ENV["MYSQL_PASSWORD"],
                'dbname' => $_ENV["MYSQL_DATABASE"]
            ]
        );
    }
);

return $container;