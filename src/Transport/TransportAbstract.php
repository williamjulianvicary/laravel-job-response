<?php

namespace Williamjulianvicary\LaravelJobResponse\Transport;

use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;
use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\ResponseCollection;
use Williamjulianvicary\LaravelJobResponse\ResponseContract;
use Williamjulianvicary\LaravelJobResponse\ResponseFactory;

abstract class TransportAbstract
{
    /**
     * The maximum number of seconds cached items should be held for (before collection).
     * 2 minutes by default - in theory, this data should be collected within a few ms.
     * @var int
     */
    public int $cacheTtl = 120;

    /**
     * By default if an exception occurs the exception will be passed as a ExceptionResponse object, however if this
     * flag is true, an exception will be raised instead.
     * @var bool
     */
    public bool $shouldThrowException = false;

    abstract public function awaitResponse(string $id, int $timeout): ResponseContract;
    abstract public function awaitResponses(string $id, int $expectedResponses, int $timeout): ResponseCollection;
    abstract public function sendResponse(string $id, $data);

    /**
     * @param $responseData
     * @return ExceptionResponse|Response|ResponseContract
     * @throws JobFailedException
     */
    protected function createResponse($responseData)
    {
        $response = ResponseFactory::create($responseData);
        if ($this->shouldThrowException && $response instanceof ExceptionResponse) {
            throw JobFailedException::fromExceptionResponse($response);
        }

        return $response;
    }

    /**
     * @param $responses
     * @return ResponseCollection
     * @throws JobFailedException
     */
    protected function createResponses($responses): ResponseCollection
    {
        $collection = new ResponseCollection();
        foreach ($responses as $response) {
            $collection->push($this->createResponse($response));
        }

        return $collection;
    }

    /**
     * @param bool $flag
     * @return TransportAbstract
     */
    public function throwExceptionOnFailure(bool $flag = false): TransportAbstract
    {
        $this->shouldThrowException = $flag;
        return $this;
    }

    /**
     * @param string $id
     * @param \Throwable $exception
     */
    public function handleFailure(string $id, \Throwable $exception): void
    {
        $data = ['exception' => $this->exceptionToArray($exception)];
        $this->sendResponse($id, $data);
    }

    /**
     * @param $id
     * @param $data
     */
    public function respond(string $id, $data): void
    {
        $data = ['response' => $data];
        $this->sendResponse($id, $data);
    }

    /**
     * @param \Throwable $exception
     * @return array
     */
    private function exceptionToArray(\Throwable $exception): array
    {
        return [
            'exception_class' => get_class($exception),
            'exception_basename' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'code' => $exception->getCode(),
            'trace' => $exception->getTraceAsString(),
            'line' => $exception->getLine()
        ];
    }
}
