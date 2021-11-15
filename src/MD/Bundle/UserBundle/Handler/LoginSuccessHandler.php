<?php

namespace MD\Bundle\UserBundle\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface {

    protected $router;
    protected $security;

    public function __construct(Router $router, SecurityContext $security) {
        $this->router = $router;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $session = new Session();

        // where "main" is the name of your firewall in security.yml
        $key = '_security.members_secured_area.target_path';

        // try to redirect to the last page, or fallback to the homepage
        if ($session->has($key)) {
            $url = $session->get($key);
            $response = new RedirectResponse($url);
            $session->remove($key);
        } else {
            if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $response = new RedirectResponse($this->router->generate('dynamicpage'));
            } elseif ($this->security->isGranted('ROLE_ADMIN')) {
                $response = new RedirectResponse($this->router->generate('dynamicpage'));
            } elseif ($this->security->isGranted('ROLE_USER')) {
                $response = new RedirectResponse($this->router->generate('fe_home'));
            } else {
                $response = new RedirectResponse($this->router->generate('dynamicpage'));
            }
        }
        return $response;
    }

}
