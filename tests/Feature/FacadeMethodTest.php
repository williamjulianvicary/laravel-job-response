<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Feature;
use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;
use Williamjulianvicary\LaravelJobResponse\Response;
use Williamjulianvicary\LaravelJobResponse\ResponseCollection;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestJob;
use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;

class FacadeMethodTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('job-response.transport', 'cache');
    }

    public function testGenerateIdent()
    {
        $this->assertIsString(LaravelJobResponse::generateIdent());
    }

    public function testTraitAwaitResponse()
    {
        $job = new TestJob();
        $response = $job->awaitResponse(10);
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testAwaitResponse()
    {
        $job = new TestJob();
        $response = LaravelJobResponse::awaitResponse($job, 10);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testAwaitResponses()
    {
        $jobs = [new TestJob(), new TestJob()];
        $response = LaravelJobResponse::awaitResponses($jobs, 10);

        $this->assertInstanceOf(ResponseCollection::class, $response);
        $this->assertCount(2, $response);
    }
}
