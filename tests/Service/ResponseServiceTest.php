<?php

namespace Riddle\RestAPIBundle\Tests\Service;

use PHPUnit\Framework\TestCase;

use Riddle\RestAPIBundle\Exception\ExceptionNotFoundException;
use Riddle\RestAPIBundle\Service\ResponseService;

class ResponseServiceTest extends TestCase
{

    public function testCreateResponse_success()
    {
        $responseService = $this->_getService();
        $data = [
            'test' => 'cats are cute!',
        ];
        $expectedData = array_merge(
            ['success' => true],
            $data
        );

        $this->assertTrue($responseService->createResponse(true, 200, $data) === $expectedData, 'Test with success=true returned data which was different than expected.');
        $expectedData['success'] = false;
        $this->assertTrue($responseService->createResponse(false, 200, $data) === $expectedData, 'Test with success=false returned data which was different than expected.');
    }

    public function testCreateResponse_invalidCode()
    {
        $responseService = $this->_getService();

        $this->expectException(\InvalidArgumentException::class);
        $this->assertTrue($responseService->createResponse(true, -1, []), 'Entered invalid code in createResponse() and the function did not throw any error.');
    }

    public function testCreateErrorResponse_default()
    {
        $msg = 'Test MSG';
        $code = 404;
        $this->_checkErrorResponse($this->_getService()->createErrorResponse($msg, $code), $code, $msg);
    }

    public function testHandleException_default()
    {
        $notFoundException = new ExceptionNotFoundException('Exception not found test.');
        $notFoundResponse = $this->_getService()->handleException($notFoundException);
        $this->_checkErrorResponse($notFoundResponse, 404, $notFoundException->getMessage());

        $defaultException = new \Exception('Normal exception test');
        $defaultExceptionResponse = $this->_getService()->handleException($defaultException);
        $this->_checkErrorResponse($defaultExceptionResponse, 403, $defaultException->getMessage());
    }

    private function _checkErrorResponse($errorResponse, int $code, string $msg)
    {
        $this->assertTrue($errorResponse['success'] === false, 'The success of an ErrorResponse should be false.');
        $this->assertTrue($errorResponse['error']['code'] === $code, 'The HTTP status code doesn\'t match the expected one.');
        $this->assertTrue($errorResponse['error']['msg'] === $msg, 'The error message is not equals the message from the exception.');
    }

    public function testItemsResponse_default()
    {
        $data = [1, 2, 3, 4];
        $this->_checkItemsResponse($this->_getService()->createItemsResponse(($data)), $data);

        $data = [];
        $this->_checkItemsResponse($this->_getService()->createItemsResponse(($data)), $data);

        $data = ''; // not a countable
        $this->expectException(\InvalidArgumentException::class);
        $this->assertTrue($this->_getService()->createItemsResponse($data), 'The createItemsResponse() function did not throw an exception although $data isn\'t a countable.');
    }

    private function _checkItemsResponse($itemsResponse, $data) 
    {
        $this->assertTrue($itemsResponse['data']['count'] === count($data), 'The count field of the itemsResponse is not equals the count of $data');
        $this->assertCount(count($itemsResponse['data']['items']), $data, 'The count of the items array field is not equals the count of the data array');
    }

    private function _getService(bool $wrapInResponse = false)
    {
        $responseService = new ResponseService();
        $responseService->setWrapInResponse($wrapInResponse);

        return $responseService;
    }


}