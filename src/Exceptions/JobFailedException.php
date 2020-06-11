<?php

namespace Williamjulianvicary\LaravelJobResponse\Exceptions;

class JobFailedException extends \Exception
{
    /**
     * @param \Throwable $e
     * @return JobFailedException
     */
    public static function fromException(\Throwable $e): JobFailedException
    {
        return new self('Job Failed', 0, $e);
    }
}
