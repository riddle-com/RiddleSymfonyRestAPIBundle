<?php

namespace Riddle\RestAPIBundle\Security;

use Riddle\RestAPIBundle\Service\ResponseService;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RiddleEntryPoint implements AuthenticationEntryPointInterface
{
    protected $router;
    protected $responseService;

    public function __construct(UrlGeneratorInterface $router, ResponseService $responseService)
    {
        $this->router = $router;
        $this->responseService = $responseService;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (!self::shouldBeCaught($request->getPathInfo())) {
            return;
        }

        return $this->responseService->createAccessDeniedResponse($authException->getMessage());
    }

    /**
     * Make it static so we can test it
     */
    public static function shouldBeCaught(string $pathInfo) :bool
    {
        $pathInfo = strtolower($pathInfo);
        $keywords = ['api', 'rest'];

        foreach ($keywords as $keyword) {
            if (strpos($pathInfo, '/' . $keyword . '/') !== false) {
                return true;
            }
        }

        return false;
    }
}
