<?php

use Phalcon\Mvc\Micro;
use App\Exceptions\HttpExceptions\AbstractHttpException;

try {
    /**
     * Read configs
     */
    $config = require __DIR__ . '/../app/config/config.php';

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Initializing DI container
     */
    /** @var \Phalcon\DI\FactoryDefault $di */
    $di = require __DIR__ . '/../app/config/di.php';

    /**
     * Initializing application
     */
    $app = new Micro();

    /**
     * Setting DI container
     */
    $app->setDI($di);

    /**
     * Read routes
     */
    require __DIR__ . "/../app/config/routes.php";

    $app->handle(
        $_SERVER["REQUEST_URI"]
    );
} catch (AbstractHttpException $e) {
    $response = $app->response;
    $response->setStatusCode($e->getCode(), $e->getMessage());
    $response->setJsonContent($e->getAppError());
    $response->send();
} catch (\Phalcon\Http\Request\Exception $e) {
    $result = [
        'code'    => 400,
        'message' => $e->getMessage()
    ];

    $app->response->setStatusCode(400, 'Bad request')
        ->setJsonContent($result)
        ->send();
} catch (\Exception $e) {
    $result = [
        'code'    => 500,
        'message' => $e->getMessage()
    ];

    $app->response->setStatusCode(500, 'Internal Server Error')
        ->setJsonContent($result)
        ->send();
}