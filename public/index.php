<?php

use Phalcon\Mvc\Micro;

try {
    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read database configuration
     */
    $container = include __DIR__ . "/../app/config/database.php";

    $app = new Micro($container);

    /**
     * Read routes
     */
    include __DIR__ . "/../app/config/routes.php";

    $app->handle(
        $_SERVER["REQUEST_URI"]
    );
} catch (\Exception $e) {
    echo $e->getMessage();
}
