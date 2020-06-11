<?php

namespace Williamjulianvicary\LaravelJobResponse;

use Williamjulianvicary\LaravelJobResponse\Transport\CacheTransport;
use Williamjulianvicary\LaravelJobResponse\Transport\RedisTransport;

class TransportFactory
{
    public const REDIS = 'redis';
    public const CACHE = 'cache';

    private const TRANSPORT_TYPES = [self::REDIS, self::CACHE];

    private const CLASS_MAP = [
        self::REDIS => RedisTransport::class,
        self::CACHE => CacheTransport::class
    ];

    private $instances = [];

    public function getTransport($transport = 'redis')
    {
        if (!in_array($transport, self::TRANSPORT_TYPES, true)) {
            throw new \InvalidArgumentException('Transport unknown.');
        }

        if (!isset($this->instances[$transport])) {
            $class = self::CLASS_MAP[$transport];
            $this->instances[$transport] = new $class();
        }

        return $this->instances[$transport];
    }
}
