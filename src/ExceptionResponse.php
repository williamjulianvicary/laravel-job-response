<?php

namespace Williamjulianvicary\LaravelJobResponse;

class ExceptionResponse implements ResponseContract
{
    private ?string $trace = null;
    private ?string $message = null;
    private ?int $code = null;
    private ?int $line = null;
    private ?string $file = null;
    private ?string $exceptionClass = null;
    private ?string $exceptionBaseName = null;

    public function __construct(array $exception)
    {
        $this->exceptionClass = $exception['exception_class'] ?? null;
        $this->exceptionBaseName = $exception['exception_basename'] ?? null;
        $this->trace = $exception['trace'] ?? null;
        $this->message = $exception['message'] ?? null;
        $this->code = $exception['code'] ?? null;
        $this->line = $exception['line'] ?? null;
        $this->file = $exception['file'] ?? null;
    }

    public function getExceptionClass()
    {
        return $this->exceptionClass;
    }

    public function getExceptionBaseName()
    {
        return $this->exceptionBaseName;
    }

    /**
     * @return mixed|string|null
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @return mixed|string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return int|mixed|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return int|mixed|null
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed|string|null
     */
    public function getFile()
    {
        return $this->file;
    }
}
