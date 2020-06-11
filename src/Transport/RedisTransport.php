<?php

namespace Williamjulianvicary\LaravelJobResponse\Transport;

use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;
use Williamjulianvicary\LaravelJobResponse\ResponseCollection;
use Williamjulianvicary\LaravelJobResponse\ResponseContract;
use Williamjulianvicary\LaravelJobResponse\Exceptions\TimeoutException;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use Williamjulianvicary\LaravelJobResponse\ResponseFactory;

class RedisTransport extends TransportAbstract implements TransportContract
{
    /**
     * @var Connection
     */
    public Connection $connection;

    public function __construct(string $connection = null)
    {
        $connection = $connection ?? (string) config('job-response.redis.connection');
        $this->connection = Redis::connection($connection);
    }

    /**
     * @param $id
     * @param $timeout
     * @return mixed
     * @throws TimeoutException
     * @throws JobFailedException
     */
    public function awaitResponse(string $id, int $timeout): ResponseContract
    {
        list($queueId, $response) = $this->connection->blpop($id, $timeout);

        if ($response === NULL) {
            throw new TimeoutException('Redis response timed out');
        }

        return $this->createResponse($this->fromStorage($response));
    }

    /**
     * @param string $id The ID to lookup the job (should match the job ident).
     * @param int $expectedResponses Number of responses to expect.
     * @param int $timeout Timeout for the request.
     * @return ResponseCollection
     * @throws TimeoutException
     * @throws JobFailedException
     */
    public function awaitResponses(string $id, int $expectedResponses, int $timeout): ResponseCollection
    {
        $responses = [];
        $timeoutExpiresAt = now()->addSeconds($timeout);
        while (true) {
            if (count($responses) >= $expectedResponses) {
                break;
            }

            if ($timeoutExpiresAt < now()) {
                throw new TimeoutException('Timed out waiting for response');
            }

            $responses[] = $this->awaitResponse($id, $timeout);
        }

        return new ResponseCollection($responses);
    }

    public function sendResponse(string $id, $data): void
    {
        $this->connection->rpush($id, $this->forStorage($data));
        $this->connection->expire($id, $this->cacheTtl);
    }

    /**
     * @param $data
     * @return string
     */
    public function forStorage($data): string
    {
        return serialize($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function fromStorage(string $data)
    {
        // No safety added, this is an internal-only serialization and should not be an attack vector.
        /** @noinspection UnserializeExploitsInspection */
        return unserialize($data);
    }
}
