<?php

namespace Williamjulianvicary\LaravelJobResponse;

class ExceptionResponse implements ResponseContract
{
    private \Throwable $exception;

    public function __construct($data)
    {
        $this->exception = $data['exception'];
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
