<?php

namespace Williamjulianvicary\LaravelJobResponse;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Williamjulianvicary\LaravelJobResponse\Skeleton\SkeletonClass
 */
class LaravelJobResponseFacade extends Facade
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
