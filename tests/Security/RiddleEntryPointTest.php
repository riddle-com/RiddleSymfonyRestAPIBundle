<?php

namespace Riddle\RestAPIBundle\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Riddle\RestAPIBundle\Security\RiddleEntryPoint;

class RiddleEntryPointTest extends WebTestCase
{

    public function testShouldBeCaught()
    {
        $rightPaths = ['api', 'rest', 'aPi', 'rEST'];

        foreach ($rightPaths as $path) {
            $this->assertTrue(RiddleEntryPoint::shouldBeCaught('/' . $path . '/'), 'RiddleEntryPoint: ' . $path . ' should have been caught.');
        }

        $wrongPaths = ['cats', 'are', 'cute!'];

        foreach ($wrongPaths as $path) {
            $this->assertFalse(RiddleEntryPoint::shouldBeCaught('/' . $path . '/'), 'RiddleEntryPoint: ' . $path . ' shouldn\'t have been caught.');
        }
    }

}