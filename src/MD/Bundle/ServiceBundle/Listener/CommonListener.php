<?php

namespace MD\Bundle\ServiceBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class CommonListener {

    private $em;

    public function __construct(EntityManager $entityManager, Router $router) {
        $this->em = $entityManager;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event) {
        $session = new Session();
        $locale = $event->getRequest()->attributes->get('_locale');

        if (empty($locale)) {
            $locale = 'en';
        }
        $session->set('_locale', $locale);
    }

}
