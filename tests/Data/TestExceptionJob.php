<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Data;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Williamjulianvicary\LaravelJobResponse\CanRespond;
use Williamjulianvicary\LaravelJobResponse\Contracts\JobCanRespond;

class TestExceptionJob implements ShouldQueue, JobCanRespond
{
    use InteractsWithQueue, Queueable, Dispatchable, CanRespond;

    public function __construct()
    {
    }

    public function handle()
    {
        throw new TestException('TestException Failure');
    }
}
