<?php

namespace Williamjulianvicary\LaravelJobResponse\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
//use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Williamjulianvicary\LaravelJobResponse\LaravelJobResponseServiceProvider;

class TestCase extends OrchestraTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations'));
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelJobResponseServiceProvider::class
        ];
    }
}
