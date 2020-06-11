<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Unit;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\Transport\CacheTransport;
use Williamjulianvicary\LaravelJobResponse\Transport\RedisTransport;
use Williamjulianvicary\LaravelJobResponse\TransportFactory;

class TransportFactoryTest extends TestCase
{

    public function testExceptionThrownWhenIncorrectTransportTypeAttempted()
    {
        $this->expectException(\InvalidArgumentException::class);
        app(TransportFactory::class)->getTransport('test');
    }

    public function testCacheTransportReturned()
    {
        $transport = app(TransportFactory::class)->getTransport('cache');
        $this->assertInstanceOf(CacheTransport::class, $transport);
    }

    public function testRedisTransportReturned()
    {
        $transport  = app(TransportFactory::class)->getTransport('redis');
        $this->assertInstanceOf(RedisTransport::class, $transport);
    }

}
