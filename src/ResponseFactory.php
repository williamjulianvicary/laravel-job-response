<?php

namespace Williamjulianvicary\LaravelJobResponse;

class ResponseFactory
{
    /**
     * @param array $response
     * @return ExceptionResponse|Response
     */
    public static function create(array $response): ResponseContract
    {
        if (isset($response['exception'])) {
            return new ExceptionResponse($response);
        }

        if (isset($response['response'])) {
            return new Response($response);
        }

        throw new \InvalidArgumentException('Response provided should be either exception or response type, neither provided.');
    }
}
