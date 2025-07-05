<?php

namespace shenoda\phpmvc\exception;


class NotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'Page not found';

    public function __construct()
    {

    }
}