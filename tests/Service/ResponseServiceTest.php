<?php

namespace Riddle\RestAPIBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Riddle\RestAPIBundle\Exception\HttpException;
use Riddle\RestAPIBundle\Exception\AccessDeniedException;
use Riddle\RestAPIBundle\Exception\NotFoundException;
use Riddle\RestAPIBundle\Exception\UnauthorizedException;
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

    public function testHandleException_httpExceptions()
    {
        $this->_checkHttpException(new HttpException('You just got redirected because why not?', 301));
        $this->_checkHttpException(new UnauthorizedException('Unauthorized exc test.'), 401);
        $this->_checkHttpException(new AccessDeniedException('Access denied exc test.'), 403);
        $this->_checkHttpException(new NotFoundException('Not found exc test.'), 404);
    }

    /**
     * If no http exception gets passed the service should return a 403 - access denied error.
     */
    public function testHandleException_otherException()
    {
        $defaultException = new \Exception('Normal exception test');
        $defaultExceptionResponse = $this->_getService()->handleException($defaultException);
        $this->_checkErrorResponse($defaultExceptionResponse, 403, $defaultException->getMessage());
    }

    /**
     * @param $codeToTest makes sure that there are no sloppy mistakes - e.g. AccessDeniedException throws 200 - would be wrong but without that check that test would be successful
     */
    private function _checkHttpException(HttpException $exc, int $codeToTest = -1)
    {
        $httpExceptionResponse = $this->_getService()->handleException($exc);
        $this->_checkErrorResponse($httpExceptionResponse, $codeToTest === -1 ? $exc->getCode() : $codeToTest, $exc->getMessage());
    }

    private function _checkErrorResponse($errorResponse, int $code, string $msg)
    {
        $this->assertTrue($errorResponse['success'] === false, 'The success of an ErrorResponse should be false.');
        $this->assertTrue($errorResponse['error']['code'] === $code, 'The HTTP status code doesn\'t match the expected one (received: ' . $errorResponse['error']['code'] . ', expected: ' . $code . ')');
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
