<?php

namespace App\Exceptions\HttpExceptions;

use App\Exceptions\HttpExceptions\AbstractHttpException;

/**
 * Class Http400Exception
 *
 * Execption class for Bad Request Error (400)
 *
 * @package App\Lib\Exceptions
 */
class Http400Exception extends AbstractHttpException
{
    function __construct(
        string $httpMessage = "Bad request", 
        array $appError = []) {
        parent::__construct($httpMessage, 400, $appError);
    }

    public function addErrorDetails(array $errors) {
        $this->appError = $errors;

        return $this;
    }
}
