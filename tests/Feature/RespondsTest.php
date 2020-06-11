<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Feature;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;
use Williamjulianvicary\LaravelJobResponse\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestExceptionJob;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestJob;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestLongRunningJob;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\Exceptions\TimeoutException;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class RespondsTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'database');
        $app['config']->set('cache.default', 'database');
        $app['config']->set('job-response.transport', 'cache');
    }

    public function testSingleResponseSuccess()
    {
        $job = new TestJob();
        $job->prepareResponse();

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $response = app(TransportContract::class)->awaitResponse($job->getResponseIdent(), 1);
        $this->assertEquals(true, $response->getData());
    }

    /**
     * @group failing
     */
    public function testSingleResponseException()
    {
        $job = new TestExceptionJob();
        $job->prepareResponse();

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $response = app(TransportContract::class)->awaitResponse($job->getResponseIdent(), 2);

        $this->assertInstanceOf(ExceptionResponse::class, $response);
    }

    public function testThreeResponses()
    {
        $ident = app(LaravelJobResponse::class)->generateIdent();

        $jobs = collect([new TestJob(), new TestJob(), new TestJob()]);
        $jobs->each(function(TestJob $job) use ($ident) {
            $job->prepareResponse($ident);
            app(Dispatcher::class)->dispatch($job);
        });

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $response = app(TransportContract::class)->awaitResponses($ident, 3, 5);

        $this->assertCount(3, $response);
    }

    public function testJobTimeOut()
    {
        $this->expectException(TimeoutException::class);
        $job = new TestLongRunningJob();
        $job->prepareResponse();

        app(Dispatcher::class)->dispatch($job);

        // Note: We're mocking a long-running job here by not actually running the queue.
        // As the queue does not finish, it is impossible for the job to respond within the timeout.
        // This is to avoid the lack of  multi-threading in PHPUNIT (i.e we cannot run the await before running the queue worker).

        app(TransportContract::class)->awaitResponse($job->getResponseIdent(), 1);
    }
}
