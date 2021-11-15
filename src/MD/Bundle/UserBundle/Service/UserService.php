<?php

namespace MD\Bundle\UserBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Container;

class UserService {

    protected $entityManager;
    protected $context;
    protected $router;
    protected $container;

    public function __construct($entityManager, Router $router, Container $container, SecurityContext $context) {
        $this->entityManager = $entityManager;
        $this->context = $context;
        $this->router = $router;
        $this->container = $container;
    }

    public function checkLogin() {
        if (!$this->context->isGranted('ROLE_USER')) {
            $ajax = $this->container->get('request')->get('ajax');
            if (!isset($ajax)) {
                return new RedirectResponse($this->router->generate('login'));
            } else {
                $array = array("error" => 1, "login" => 1);
                return json_encode($array, TRUE);
            }
        }
        return TRUE;
    }

}
