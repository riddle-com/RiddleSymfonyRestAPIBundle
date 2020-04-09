<?php

namespace Riddle\RestAPIBundle\Exception;

class NotFoundException extends HttpException
{
    
    public function __construct(string $message)
    {
        $this->message = $message;
        
        parent::__construct($message, 404);
    }

}