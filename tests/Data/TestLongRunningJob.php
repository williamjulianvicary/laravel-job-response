<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests\Data;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Williamjulianvicary\LaravelJobResponse\CanRespond;
use Williamjulianvicary\LaravelJobResponse\Contracts\JobCanRespond;

class TestLongRunningJob implements ShouldQueue, JobCanRespond
{
    use InteractsWithQueue, Queueable, Dispatchable, CanRespond;

    public $sleep;

    public function __construct($sleep = 2)
    {
        $this->sleep = $sleep;
    }

    public function handle()
    {
        usleep($this->sleep * 1000 * 1000);
        $this->respond(true);
    }
}
