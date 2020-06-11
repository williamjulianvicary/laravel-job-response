<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Feature;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Williamjulianvicary\LaravelJobResponse\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\ResponseCollection;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestJob;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\Exceptions\TimeoutException;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;
use Illuminate\Support\Facades\Config;

class RedisTransportTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('job-response.transport', 'redis');
        $app['config']->set('queue.default', 'sync');
    }

    public function testJobTransportSuccess()
    {
        $job = new TestJob();
        $response = $job->awaitResponse(10);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testMultipleJobSuccess()
    {
        Config::set('queue.default', 'database');
        $ident = app(LaravelJobResponse::class)->generateIdent();

        $jobs = [new TestJob(), new TestJob()];
        $jobs = collect($jobs)->map(function($job) use ($ident) {
            $job->prepareResponse($ident);
            app(Dispatcher::class)->dispatch($job);
        });

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $responses = app(TransportContract::class)->awaitResponses($ident, 2, 5);

        $this->assertInstanceOf(ResponseCollection::class, $responses);
        $this->assertCount(2, $responses);
    }

    public function testTimeoutOnNoQueueResponse()
    {
        $this->expectException(TimeoutException::class);
        app(TransportContract::class)->awaitResponse('dummy', 1);
    }

    public function testTimeOnNoResponseMultipleResponses()
    {
        Config::set('queue.default', 'database');
        $this->expectException(TimeoutException::class);

        app(TransportContract::class)->awaitResponses('dummy', 3, -1);
    }
}
