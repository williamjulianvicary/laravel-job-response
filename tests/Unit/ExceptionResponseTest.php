<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Unit;
use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestException;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\TransportFactory;

class ExceptionResponseTest extends TestCase
{

    public function testExceptionResponseGetters()
    {
        $exception = new TestException();
        $data = [
            'exception_class' => get_class($exception),
            'exception_basename' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'code' => $exception->getCode(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];

        $exceptionResponse = new ExceptionResponse($data);
        $this->assertEquals(TestException::class, $exceptionResponse->getExceptionClass());
        $this->assertEquals('TestException', $exceptionResponse->getExceptionBaseName());
        $this->assertEquals($exception->getMessage(), $exceptionResponse->getMessage());
        $this->assertEquals($exception->getFile(), $exceptionResponse->getFile());
        $this->assertEquals($exception->getCode(), $exceptionResponse->getCode());
        $this->assertEquals($exception->getTraceAsString(), $exceptionResponse->getTrace());
        $this->assertEquals($exception->getLine(), $exceptionResponse->getLine());
    }
}
