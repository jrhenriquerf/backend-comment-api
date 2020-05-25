<?php

$di = new \Phalcon\DI\FactoryDefault();

$di->setShared(
  'response',
  function () {
      $response = new \Phalcon\Http\Response();
      $response->setContentType('application/json', 'utf-8');

      return $response;
  }
);

$di->loadFromPhp("app/config/services.php");

$di->setShared('config', $config);

/** Database */
$di->set(
  "db",
  function () use ($config) {
      return new \Phalcon\Db\Adapter\Pdo\Mysql(
        [
          "host"     => $config->database->host,
          "username" => $config->database->username,
          "password" => $config->database->password,
          "dbname"   => $config->database->dbname,
        ]
      );
  }
);

return $di;