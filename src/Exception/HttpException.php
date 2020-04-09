<?php

namespace Riddle\RestAPIBundle\Exception;

class HttpException extends \Exception
{
    protected $code;
    protected $message;
    
    public function __construct(string $message, int $code)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
