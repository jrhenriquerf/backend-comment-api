<?php

namespace App\Exceptions\ServiceExceptions;

/**
 * Class ServiceException
 *
 * Execption class for services
 *
 * @package App\Lib\Exceptions
 */
class ServiceException extends \RuntimeException
{
    protected $appError;

    function __construct(
        string $message = "Failed to run service", 
        int $code = 500,
        array $appError = []) 
    {
        $this->code = $code;
        $this->message = $message;
        $this->appError = $appError;
    }

    public function addErrorDetails(array $errors) {
        $this->appError = $errors;

        return $this;
    }
}