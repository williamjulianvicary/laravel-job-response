# Laravel Job Response - Making your jobs respond

[![Latest Version on Packagist](https://img.shields.io/packagist/v/williamjulianvicary/laravel-job-response.svg?style=flat-square)](https://packagist.org/packages/williamjulianvicary/laravel-job-response)
[![Build Status](https://img.shields.io/travis/williamjulianvicary/laravel-job-response/master.svg?style=flat-square)](https://travis-ci.org/williamjulianvicary/laravel-job-response)
[![Total Downloads](https://img.shields.io/packagist/dt/williamjulianvicary/laravel-job-response.svg?style=flat-square)](https://packagist.org/packages/williamjulianvicary/laravel-job-response)

Have you ever needed to run a Laravel job (or multiple jobs), wait for the response and then use that response? This is exactly the functionality this package provides. 

## Installation

You can install the package via composer:

```bash
composer require williamjulianvicary/laravel-job-response
```

## Requirements

- PHP >= 8.1
- Laravel >= 9.0 (While not tested on prior versions may work)

## Usage

In your `Job` use the `CanRespond` trait and add implement the `JobCanRespond` contract.

``` php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Williamjulianvicary\LaravelJobResponse\CanRespond;
use Williamjulianvicary\LaravelJobResponse\Contracts\JobCanRespond;

class TestJob implements ShouldQueue, JobCanRespond
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
        
        // $response is an instance of Response or ExceptionResponse
        $data = $response->getData(); // 'Success'
        // or 
        $exception = $response; // JobFailedException
    }
}
```

Or alternatively, run multiple jobs and await the responses
``` php
<?php

namespace App\Services;
namespace Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;

class Service
{
    public function test()
    {
        $jobs = [new TestJob(), new TestJob()];
        $responses = LaravelJobResponse::awaitResponses($jobs); // ResponseCollection
        
        foreach ($responses as $response) {
            if ($response instanceof ExceptionResponse) {
                echo "Exception: " . $response->getMessage() . "\n";
            } else {
                echo "Response: " . $response->getData() . "\n";
            }
        }
    }
}   
```

### Responses

By default, the package responds in three ways:

- `ResponseCollection` - When multiple responses are expected, a ResponseCollection will be 
returned containing `Response` and/or `ExceptionResponse` objects.
- `Response` - A successful response object.
- `ExceptionResponse` - When a job fails the exception is caught and passed back.

### (Optional) Handling Exceptions 

By default a `ExceptionResponse` object is created with a `$exceptionResponse->getException()` method available to allow you to
review the exception thrown from the Job. However, this can lead to some extra boilerplate code to handle this, so instead we've
an optional method available that will re-throw these exceptions.

To enable this, use the Facade to update the `throwExceptionsOnFailures` flag
```php
use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;
[...]
LaravelJobResponse::throwExceptionsOnFailures(true);
```

Now whenever a await is issued, if an exception is encountered from the job, a `JobFailedException` will be raised:
```php
<?php

namespace App\Services;
use Williamjulianvicary\LaravelJobResponse\Facades\LaravelJobResponse;
use Williamjulianvicary\LaravelJobResponse\Exceptions\JobFailedException;

class Service
{
    public function test()
    {
        $jobs = [new TestJob(), new TestJob()];
        try {
           $responses = LaravelJobResponse::awaitResponses($jobs);
        } catch (JobFailedException $exception) {
            // One of the jobs failed.
            $exception->getTrace(); // The exception trace string thrown by the job.
        }       
       
    }
}  
```

### Methods
```php
<?php
// Methods available on your jobs

// Await a response for this job, optionally accepts a timeout and bool whether a exception should be raised if the job fails.
// Responds with either Response or ExceptionResponse objects.
$job->awaitResponse($timeout = 10, $throwException = false);  

$job->respond($mixed); // Should be used within the handle() method of the job to respond appropriately.
$job->respondWithException(\Throwable); // If you override the failed() method, this method responds with an exception.

// Facade methods

// Await a response for the given job.
LaravelJobResponse::awaitResponse(JobCanRespond $job, $timeout=10);

// Await responses from the provided job array.
LaravelJobResponse::awaitResponses(array $jobs, $timeout=10);

// Change how exceptions are handled (see above).
LaravelJobResponse::throwExceptionOnFailure(false);
```

### Troubleshooting

There are a few quirks within Laravel that you may run into with this package.

- When running with a `sync` driver, Exceptions will not be caught - this is because Laravel does not natively catch them with the Sync driver
and it is impossible for our package to pick them up. If you need to handle exceptions with this driver, use `$job->fail($exception);` instead.


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
