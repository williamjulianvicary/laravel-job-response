<?php

namespace Williamjulianvicary\LaravelJobResponse\Exceptions;

use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;

class JobFailedException extends \Exception
{
    private ExceptionResponse $exceptionResponse;

    public function __construct($message = '', $code = 0, \Throwable $previous = null, ?ExceptionResponse $exceptionResponse = null)
    {
        parent::__construct($message, $code = 0, $previous = null);
        $this->exceptionResponse = $exceptionResponse;
    }

    public function getExceptionResponse()
    {
        return $this->exceptionResponse;
    }

    /**
     * @param ExceptionResponse $exceptionResponse
     * @return JobFailedException
     */
    public static function fromExceptionResponse(ExceptionResponse $exceptionResponse): JobFailedException
    {
        return new self('Job Failed', 0, null, $exceptionResponse);
    }
}
