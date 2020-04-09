<?php

namespace Riddle\RestAPIBundle\Exception;

class AccessDeniedException extends HttpException
{
    
    public function __construct(string $message)
    {
        $this->message = $message;
        
        parent::__construct($message, 403);
    }

}