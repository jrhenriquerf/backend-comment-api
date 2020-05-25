<?php

namespace App\Exceptions\HttpExceptions;

use App\Exceptions\HttpExceptions\AbstractHttpException;

/**
 * Class Http403Exception
 *
 * Execption class for Forbidden request error (403)
 *
 * @package App\Lib\Exceptions
 */
class Http403Exception extends AbstractHttpException
{
    function __construct(
        string $httpMessage = "Forbidden", 
        array $appError = []) {
        parent::__construct($httpMessage, 403, $appError);
    }

    public function addErrorDetails(array $errors) {
        $this->appError = $errors;

        return $this;
    }
}
