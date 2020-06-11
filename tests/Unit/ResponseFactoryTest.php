<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Unit;
use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\ResponseFactory;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestException;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;

class ResponseFactoryTest extends TestCase
{
    public function testExceptionThrownWhenIncorrectResponseTypeAttempted()
    {
        $this->expectException(\InvalidArgumentException::class);
        ResponseFactory::create(['test' => 'test']);
    }

    public function testExceptionResponseReturned()
    {
        $response = ['exception' => new TestException()];

        $response = ResponseFactory::create($response);
        $this->assertInstanceOf(ExceptionResponse::class, $response);
        $this->assertInstanceOf(TestException::class, $response->getException());
    }

    public function testResponseReturned()
    {
        $response = ['response' => 'test'];

        $response = ResponseFactory::create($response);
        $this->assertInstanceOf(Response::class, $response);
    }

}
