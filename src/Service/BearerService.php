<?php

namespace Riddle\RestAPIBundle\Service;

use Riddle\RestAPIBundle\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class BearerService
{
    
    private $token;

    public function __construct($token)
    {
        $this->token = $token . md5(date('Y-m-d'));
    }

    /**
     * Checks only if $bearer matches the given token.
     * 
     * @param bool $throw pass false if this function should return a boolean instead of throwing an AccessDeniedException
     * @throws AccessDeniedException
     */
    public function checkBearer($bearer, bool $throw = true)
    {
        if (!$bearer || $this->token !== $bearer) {
            if ($throw) {
                throw new AccessDeniedException('Invalid token.');
            }
            
            return false;
        }
    }

     /**
     * Checks header authorization (Bearer) with the help of a Request object
     * 
     * @param Request $request
     * @param bool $throw pass false if this function should return a boolean instead of throwing an AccessDeniedException
     * @throws AccessDeniedException
     */
    public function checkRequest(Request $request, bool $throw = true)
    {
        return $this->checkBearer($this->_getBearer($request), $throw);
    }


    protected function _getBearer(Request $request)
    {
        $bearer = trim($request->headers->get("Authorization"));

        if (!$bearer) {
            return false;
        }

        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            return false;
        }

        return $matches[1];
    }
}