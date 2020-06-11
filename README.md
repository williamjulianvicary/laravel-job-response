# Laravel Job Response - Making your jobs respond

[![Latest Version on Packagist](https://img.shields.io/packagist/v/williamjulianvicary/laravel-job-response.svg?style=flat-square)](https://packagist.org/packages/williamjulianvicary/laravel-job-response)
[![Build Status](https://img.shields.io/travis/williamjulianvicary/laravel-job-response/master.svg?style=flat-square)](https://travis-ci.org/williamjulianvicary/laravel-job-response)
[![Quality Score](https://img.shields.io/scrutinizer/g/williamjulianvicary/laravel-job-response.svg?style=flat-square)](https://scrutinizer-ci.com/g/williamjulianvicary/laravel-job-response)
[![Total Downloads](https://img.shields.io/packagist/dt/williamjulianvicary/laravel-job-response.svg?style=flat-square)](https://packagist.org/packages/williamjulianvicary/laravel-job-response)

Have you ever needed to run a Laravel job (or multiple jobs), wait for the response and then use that response? This is exactly the functionality this package provides. 

## Installation

You can install the package via composer:

```bash
composer require williamjulianvicary/laravel-job-response
```

## Requirements

- PHP >= 7.4
- Laravel >= 7.0 (While not tested on prior versions may work)

## Usage

In your `Job` use the `CanRespond` trait.

``` php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Williamjulianvicary\LaravelJobResponse\CanRespond;

class TestJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable, CanRespond;

    public function __construct()
    {

    }

    public function handle()
    {
        $this->respond('Success');
    }
}
```

Then in your Service/Controller/elsewhere, await a response from your job.

``` php
<?php

namespace App\Services;

class Service
{
    public function test()
    {
        $job = new TestJob();
        $response = $job->awaitResponse();
        
        // ... Use $response.
    }
}
```

Or alternatively, run multiple jobs and await the responses
``` php
<?php

namespace App\Services;
namespace Williamjulianvicary\LaravelJobResponse\LaravelJobResponseFacade;

class Service
{
    public function test()
    {
        $jobs = [new TestJob(), new TestJob()];
        $responses = LaravelJobResponseFacade::awaitResponses($jobs);
        
        // ... Use $responses.
    }
}   
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [William Julian-Vicary](https://github.com/williamjulianvicary)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
