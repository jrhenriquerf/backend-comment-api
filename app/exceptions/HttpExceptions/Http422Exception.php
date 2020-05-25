<?php

namespace App\Exceptions\HttpExceptions;

use App\Exceptions\HttpExceptions\AbstractHttpException;

/**
 * Class Http422Exception
 *
 * Execption class for Unprocessable entity Error (422)
 *
 * @package App\Lib\Exceptions
 */
class Http422Exception extends AbstractHttpException
{
    function __construct(
        string $httpMessage = "Unprocessable entity",
        array $appError = []) {
        parent::__construct($httpMessage, 422, $appError);
    }
}
