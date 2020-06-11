<?php

namespace Williamjulianvicary\LaravelJobResponse\Transport;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\ResponseCollection;
use Williamjulianvicary\LaravelJobResponse\ResponseContract;
use Williamjulianvicary\LaravelJobResponse\Exceptions\TimeoutException;
use Williamjulianvicary\LaravelJobResponse\ResponseFactory;

class CacheTransport extends TransportAbstract implements TransportContract
{
    /**
     * How frequently should we poll the cache for the response?
     * @var int
     */
    public int $millisecondPollWait = 250;

    /**
     * How long should we wait to acquire a lock, at most?
     * @var int
     */
    public int $lockWaitSeconds = 5;

    /**
     * How long should the lock be held for?
     * @var int
     */
    public int $lockHoldSeconds = 5;

    /**
     * The suffix used for the lock key.
     * @var string
     */
    private string $lockIdSuffix = ':lock';

    /**
     * Instance of the cache store used for all storage/collection calls.
     * @var Repository
     */
    private Repository $cacheStore;

    public function __construct(string $store = null)
    {
        $store = $store ?? (string) config('job-transport.cache.store');
        $this->cacheStore = Cache::store($store);

        if (!method_exists($this->cacheStore->getStore(), 'lock')) {
            throw new \InvalidArgumentException(
                'The cache driver provided does not support locking. Try Array, Database, Memcached, Redis.'
            );
        }
    }


    /**
     * @param string $id
     * @param int $timeout
     * @param int $expectedResponses
     * @return array
     * @throws TimeoutException
     */
    public function _awaitResponse(string $id, int $timeout, int $expectedResponses = 1): array
    {
        $timeoutAt = now()->addSeconds($timeout);

        $response = null;
        while (true) {
            if ($timeoutAt < now()) {
                throw new TimeoutException('Timed out while waiting for a response');
            }

            if ($response = $this->cacheStore->get($id)) {
                if (count($response) >= $expectedResponses) {
                    break;
                }
            }

            usleep($this->millisecondPollWait * 1000);
        }

        return $response;
    }

    /**
     * @param string $id
     * @param int $timeout
     * @return ResponseContract
     * @throws TimeoutException
     * @throws JobFailedException
     */
    public function awaitResponse(string $id, int $timeout): ResponseContract
    {
        $response = $this->_awaitResponse($id, $timeout, 1);
        $response = $response[0];
        return $this->createResponse($response);
    }

    /**
     * @param string $id
     * @param int $expectedResponses Number of responses to expect
     * @param int $timeout
     * @return ResponseCollection
     * @throws TimeoutException
     * @throws JobFailedException
     */
    public function awaitResponses(string $id, int $expectedResponses, int $timeout): ResponseCollection
    {
       return $this->createResponses($this->_awaitResponse($id, $timeout, $expectedResponses));
    }

    /**
     * @param $id
     * @param $data
     * @throws TimeoutException
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function sendResponse(string $id, $data): void
    {
        $lock = $this->cacheStore->lock($id . $this->lockIdSuffix, $this->lockHoldSeconds);

        try {
            $lock->block($this->lockWaitSeconds);
            $cacheData = $this->cacheStore->get($id, []);
            $cacheData[] = $data;
            $this->cacheStore->put($id, $cacheData, $this->cacheTtl);
        } catch (LockTimeoutException $e) {
            throw new TimeoutException('Timed out attempting to acquire cache lock - something went wrong.');
        } finally {
            optional($lock)->release();
        }
    }
}
