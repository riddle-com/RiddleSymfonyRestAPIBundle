<?php

namespace Riddle\RestAPIBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Riddle\RestAPIBundle\Service\BearerService;
use Riddle\RestAPIBundle\Exception\AccessDeniedException;

class BearerServiceTest extends TestCase
{

    public function testCheckBearer_default_fromString()
    {
        $service = $this->_getService();

        $this->assertTrue($service->checkBearer('R1ddl3.s3cr3t_:)' . md5(date('Y-m-d'))) !== false, 'bearerService::checkBearer(..) returned false with the right bearer');

        $this->expectException(AccessDeniedException::class);
        $this->assertTrue($service->checkBearer('wrong bearer'), 'BearerService didn\'t throw an error although the bearer was wrong.');
    }

    private function _getService(): BearerService
    {
        return new BearerService('R1ddl3.s3cr3t_:)');
    }

}