<?php

namespace MD\Bundle\ServiceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Newsletter;
use MD\Bundle\CMSBundle\Form\NewsletterType;

/**
 * Newsletter controller.
 *
 * @Route("/locale")
 */
class LocaleController extends Controller {

    /**
     * @Route("/change/{current}/{locale}/", name="locale_change")
     */
    public function setLocaleAction($current, $locale) {
        $this->get('request')->setLocale($locale);
        $referer = str_replace($current, $locale, $this->getRequest()->headers->get('referer'));
        return $this->redirect($referer);
    }

    public function getCurrentLoacle() {
        $request = $this->get('request');
        return $request->getLocale();
    }

}
