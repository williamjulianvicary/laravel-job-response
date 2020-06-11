<?php

namespace Williamjulianvicary\LaravelJobResponse\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelJobResponse extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-job-response';
    }
}
