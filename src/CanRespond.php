<?php

namespace Williamjulianvicary\LaravelJobResponse;

use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

trait CanRespond
{
    public string $responseIdent;

    public function prepareResponse(?string $id = null): self
    {
        // Don't re-prepare the response if it has already been prepared.
        if (!isset($this->responseIdent)) {
            $this->responseIdent = $id ?? (string)LaravelJobResponse::generateIdent(self::class);
        }

        return $this;
    }

    public function failed(\Throwable $exception): void
    {
        $this->respondWithException($exception);
    }

    /**
     * @param $data
     */
    public function respond($data): void
    {
        app(TransportContract::class)->respond($this->getResponseIdent(), $data);
    }

    public function respondWithException(\Throwable $exception): void
    {
        app(TransportContract::class)->handleFailure($this->getResponseIdent(), $exception);
    }

    public function getResponseIdent(): string
    {
        return $this->responseIdent;
    }

    /**
     * Dispatch the current job class and await a response.
     *
     * @param int $timeout default waits 10 seconds for a response
     * @param bool $throwException should we throw an exception on failures?
     * @return mixed
     */
    public function awaitResponse($timeout = 10, $throwException = false): ResponseContract
    {
        return LaravelJobResponse::throwExceptionOnFailure($throwException)->awaitResponse($this, $timeout);
    }
}
