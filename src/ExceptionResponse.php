<?php

namespace Williamjulianvicary\LaravelJobResponse;

class ExceptionResponse implements ResponseContract
{
    private \Throwable $exception;

    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
