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
            throw JobFailedException::fromException($response->getException());
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
        $this->flattenExceptionBacktrace($exception);
        $data = ['exception' => $exception];
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
     * @see https://gist.github.com/nh-mike/fde9f69a57bc45c5b491d90fb2ee08df
     * @noinspection PhpDocMissingThrowsInspection
     * @codeCoverageIgnore
     */
    private function flattenExceptionBacktrace(\Throwable $exception) {
        if ($exception instanceof \Exception) {
            $traceProperty = (new \ReflectionClass('Exception'))->getProperty('trace');
        } else {
            $traceProperty = (new \ReflectionClass('Error'))->getProperty('trace');
        }
        $traceProperty->setAccessible(true);

        $flatten = function(&$value, $key) {
            if ($value instanceof \Closure) {
                $closureReflection = new \ReflectionFunction($value);
                $value = sprintf(
                    '(Closure at %s:%s)',
                    $closureReflection->getFileName(),
                    $closureReflection->getStartLine()
                );
            } elseif (is_object($value)) {
                $value = sprintf('object(%s)', get_class($value));
            } elseif (is_resource($value)) {
                $value = sprintf('resource(%s)', get_resource_type($value));
            }
        };

        $previousexception = $exception;
        do {
            if ($previousexception === NULL) {
                break;
            }
            $exception = $previousexception;
            $trace = $traceProperty->getValue($exception);
            foreach($trace as &$call) {
                array_walk_recursive($call['args'], $flatten);
            }
            $traceProperty->setValue($exception, $trace);
        } while($previousexception = $exception->getPrevious());

        $traceProperty->setAccessible(false);
    }
}
