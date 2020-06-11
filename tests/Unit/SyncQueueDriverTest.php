<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Unit;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestJob;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;

class SyncQueueDriverTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('job-response.transport', 'cache');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('queue.default'. 'sync');
    }

    public function testSyncDriverWorksWithSuccess()
    {
        $job = new TestJob();
        $response = $job->awaitResponse(2);
        $this->assertEquals(true, $response->getData());
    }
}
