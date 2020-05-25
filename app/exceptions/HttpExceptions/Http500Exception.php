<?php

namespace App\Exceptions\HttpExceptions;

use App\Exceptions\HttpExceptions\AbstractHttpException;

/**
 * Class Http500Exception
 *
 * Execption class for Internal Server Error (500)
 *
 * @package App\Lib\Exceptions
 */
class Http500Exception extends AbstractHttpException
{
    function __construct(
        string $httpMessage = "Internal Server Error", 
        array $appError = []) {
        parent::__construct($httpMessage, 500, $appError);
    }
}
