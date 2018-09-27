<?php

namespace Pumukit\SecurityBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Pumukit\SecurityBundle\Services\CASService;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $router;
    protected $casService;

    public function __construct(UrlGeneratorInterface $router, CASService $casService)
    {
        $this->router = $router;
        $this->casService = $casService;
    }

    public function onLogoutSuccess(Request $request)
    {
        $url = $this->router->generate('pumukit_webtv_index_index', array(), true);
        $this->casService->logoutWithRedirectService($url);
    }
}
