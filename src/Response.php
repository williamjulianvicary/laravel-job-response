<?php

namespace Williamjulianvicary\LaravelJobResponse;

class Response implements ResponseContract
{
    private $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
