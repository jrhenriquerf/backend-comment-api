<?php

namespace App\Exceptions\HttpExceptions;

/**
 * Class AbstractHttpException
 *
 * Abstract class for http exceptions
 *
 * @package App\Lib\Exceptions
 */
abstract class AbstractHttpException extends \RuntimeException
{
    protected $appError;

    function __construct(string $httpMessage, int $httpCode, array $appError = []) {
        $this->code = $httpCode;
        $this->message = $httpMessage;
        $this->appError = $appError;
    }

    public function getAppError() {
        return $this->appError;
    }
}
