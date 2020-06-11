<?php

namespace Williamjulianvicary\LaravelJobResponse\Contracts;

interface JobCanRespond
{
    public function prepareResponse(?string $id = null): self;
    public function failed(?\Throwable $exception = null): void;
    public function respond($data): void;
    public function respondWithException(\Throwable $exception): void;
    public function getResponseIdent(): string;
    public function awaitResponse($timeout = 10);
}
