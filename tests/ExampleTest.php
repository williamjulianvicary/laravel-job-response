<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests;

use Orchestra\Testbench\TestCase;
use Williamjulianvicary\LaravelJobResponse\LaravelJobResponseServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [LaravelJobResponseServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
