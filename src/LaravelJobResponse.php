<?php

namespace Williamjulianvicary\LaravelJobResponse;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Str;
use Williamjulianvicary\LaravelJobResponse\Contracts\JobCanRespond;
use Williamjulianvicary\LaravelJobResponse\Transport\TransportContract;

class LaravelJobResponse
{
    public bool $throwExceptionOnFailure = false;

    public function generateIdent(string $class = null): string
    {
        return ($class ?? self::class) . ':rpc:' . Str::random(80);
    }

    /**
     * @param bool $flag
     * @return LaravelJobResponse
     */
    public function throwExceptionOnFailure(bool $flag = false): LaravelJobResponse
    {
        $this->throwExceptionOnFailure = $flag;
        return $this;
    }

    /**
     * @param JobCanRespond $job
     * @param int $timeout
     * @return ResponseContract
     */
    public function awaitResponse(JobCanRespond $job, int $timeout = 10): ResponseContract
    {
        // Dispatch the job
        $job->prepareResponse();
        app(Dispatcher::class)->dispatch($job);

        return app(TransportContract::class)
            ->throwExceptionOnFailure($this->throwExceptionOnFailure)
            ->awaitResponse($job->getResponseIdent(), $timeout);
    }

    /**
     * @param array $jobs
     * @param int $timeout
     * @return ResponseCollection
     */
    public function awaitResponses(array $jobs, int $timeout = 10): ResponseCollection
    {
        $queueIdent = $this->generateIdent();

        $jobCollection = collect($jobs);
        $jobCollection->each(static function(JobCanRespond $job) use ($queueIdent) {
            $job->prepareResponse($queueIdent);
            app(Dispatcher::class)->dispatch($job);
        });

        return app(TransportContract::class)
            ->throwExceptionOnFailure($this->throwExceptionOnFailure)
            ->awaitResponses($queueIdent, $jobCollection->count(), $timeout);
    }
}
