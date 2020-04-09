<?php

/**
 * This service generates all the possible responses our REST API can return
 */

namespace Riddle\RestAPIBundle\Service;

use InvalidArgumentException;
use Riddle\RestAPIBundle\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Riddle\RestAPIBundle\Exception\NotFoundException;

class ResponseService
{
    private $wrapInResponse = true;

    /**
     * @param $exc the exception that was thrown and which should be handled accordingly
     * @return JsonResponse
     */
    public function handleException(\Exception $exc)
    {
        $msg = $exc->getMessage();

        if ($exc instanceof HttpException) {
            return $this->createErrorResponse($exc->getMessage(), $exc->getCode());
        }
        
        return $this->createAccessDeniedResponse($msg); // "default case"
    }

    public function createSuccessResponse($data)
    {
        return $this->createResponse(true, 200, ['data' => $data]);
    }

    public function createItemsResponse($data)
    {
        if (!is_countable($data)) {
            throw new InvalidArgumentException('The argument $data is not a countable and therefore cannot be used for an items response.');
        }

        return $this->createSuccessResponse([
            'count' => count($data),
            'items' => $data
        ]);
    }

    public function createAccessDeniedResponse($msg = 'Access denied.')
    {
        return $this->createErrorResponse($msg, 403);
    }

    public function createNotFoundResponse($msg = 'Resource not found.')
    {
        return $this->createErrorResponse($msg, 404);
    }

    public function createErrorResponse($msg, $code)
    {
        return $this->createResponse(false, $code, ['error' => [
            'msg' => $msg,
            'code' => $code,
        ]]);
    }

    /**
     * Creates a response of the following structure:
     * [
     *  success => $success,
     *  $data
     * ]
     *
     * @param $success defines whether the response should display a success/failure
     * @param $httpcode the http code is included in the response
     * @param $data gets appended to the response array.
     * @return JsonResponse which can be returned in any controller
     *
     */
    public function createResponse(bool $success, int $httpCode, array $data)
    {
        $this->_checkHttpCode($httpCode);
        $responseData = array_merge(['success' => $success], $data);

        if (!$this->wrapInResponse) {
            return $responseData;
        }

        return new JsonResponse(
            $responseData,
            $httpCode
        );
    }

    /**
     * Checks if the HTTP response code is valid.
     * For simplicity reasons we only check if the code is positive and if not an exception gets thrown (see below)
     *
     * @throws InvalidArgumentException if the code is not valid
     */
    private function _checkHttpCode(int $code)
    {
        if ($code <= 0) {
            throw new \InvalidArgumentException('Invalid http code: ' . $code);
        }
    }

    /**
     * Change this value if you want the functions to return arrays of data instead of a whole JsonResponse.
     * If $wrapInResponse is false all the create response functions return your data wrapped into a JsonResponse with the according status code
     */
    public function setWrapInResponse(bool $wrapInResponse)
    {
        $this->wrapInResponse = $wrapInResponse;
    }
}
