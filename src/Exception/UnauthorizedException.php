<?php

namespace Riddle\RestAPIBundle\Exception;

class UnauthorizedException extends HttpException
{
    public function __construct(string $message)
    {
        $this->message = $message;
        
        parent::__construct($message, 401);
    }
}
