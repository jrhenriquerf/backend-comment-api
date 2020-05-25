<?php

namespace App\Exceptions\HttpExceptions;

use App\Exceptions\HttpExceptions\AbstractHttpException;

/**
 * Class Http404Exception
 *
 * Execption class for Not Found Error (404)
 *
 * @package App\Lib\Exceptions
 */
class Http404Exception extends AbstractHttpException
{
    function __construct(
        string $httpMessage = "Not Found", 
        array $appError = []) {
        parent::__construct($httpMessage, 404, $appError);
    }
}
