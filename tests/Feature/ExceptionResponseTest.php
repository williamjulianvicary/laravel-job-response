<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Feature;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Williamjulianvicary\LaravelJobResponse\ExceptionResponse;
use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;
use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestException;
use Williamjulianvicary\LaravelJobResponse\Tests\Data\TestExceptionJob;
use Williamjulianvicary\LaravelJobResponse\Tests\TestCase;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class ExceptionResponseTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('queue.default', 'database');
        $app['config']->set('cache.default', 'database');
        $app['config']->set('job-response.transport', 'cache');
    }

    /**
     * @group failing
     */
    public function testThrowsJobFailedException()
    {
        $this->expectException(JobFailedException::class);
        $job = new TestExceptionJob();
        $job->prepareResponse();

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        $response = app(TransportContract::class)->throwExceptionOnFailure(true)->awaitResponse($job->getResponseIdent(), 1);
    }

    public function testThrowsJobFailedExceptionGetter()
    {
        try {
            $job = new TestExceptionJob();
            $job->prepareResponse();

            app(Dispatcher::class)->dispatch($job);

            Artisan::call('queue:work', [
                '--once' => 1,
            ]);

            $response = app(TransportContract::class)->throwExceptionOnFailure(true)->awaitResponse($job->getResponseIdent(), 1);
        } catch (JobFailedException $e) {
            $exceptionResponse = $e->getExceptionResponse();
        }

        $this->assertInstanceOf(ExceptionResponse::class, $exceptionResponse);
    }

    /**
     * @group failing
     */
    public function testDoesNotThrowJobFailedException()
    {
        $job = new TestExceptionJob();
        $job->prepareResponse();

        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        /** @var ExceptionResponse $response */
        $response = app(TransportContract::class)->throwExceptionOnFailure(false)->awaitResponse($job->getResponseIdent(), 1);
        $this->assertInstanceOf(ExceptionResponse::class, $response);
        $this->assertEquals('TestException', $response->getExceptionBaseName());
        $this->assertEquals(TestException::class, $response->getExceptionClass());
    }

    /**
     * @group new
     */
    public function testFacadeThrowsJobFailedException()
    {
        $id = 'test';
        $this->expectException(JobFailedException::class);
        $job = new TestExceptionJob();
        $job->prepareResponse($id);
        app(Dispatcher::class)->dispatch($job);

        Artisan::call('queue:work', [
            '--once' => 1,
        ]);

        LaravelJobResponse::throwExceptionOnFailure(true);
        $response = LaravelJobResponse::awaitResponse($job, 1);
    }
}
