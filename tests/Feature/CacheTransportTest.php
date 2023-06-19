<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Feature;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\Exceptions\TimeoutException;
use Williamjulianvicary\LaravelJobResponse\Transport\CacheTransport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class CacheTransportTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('job-response.transport', 'cache');
        $app['config']->set('cache.default', 'array');
    }

    public function testExceptionThrownForIncorrectStoreType()
    {
        Config::set('cache.default', 'apc');
        $this->expectException(\InvalidArgumentException::class);
        new CacheTransport();
    }

    public function testExceptionThrownWhenLockCannotBeClaimed()
    {
        $this->expectException(TimeoutException::class);
        $lock = Cache::lock('test:lock', 30);

        $lock->get();
        $cacheTransport = new CacheTransport();
        $cacheTransport->lockWaitSeconds = 1;
        $cacheTransport->sendResponse('test', 'test');

        $lock->release();
    }
}
