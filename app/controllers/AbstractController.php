<?php

namespace App\Controllers;

/**
 * Class AbstractController
 *
 * @property \Phalcon\Http\Request              $request
 * @property \Phalcon\Http\Response             $htmlResponse
 * @property \Phalcon\Db\Adapter\Pdo\Mysql      $db
 * @property \Phalcon\Config                    $config
 * @property \App\Services\UserService          $userService
 * @property \App\Models\User                   $user
 */
abstract class AbstractController extends \Phalcon\DI\Injectable
{

}