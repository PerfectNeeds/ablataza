<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Utils\Validate;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Form\CaptchaType;
use Symfony\Component\HttpFoundation\Response;
use MD\Bundle\ServiceBundle\Lib\SimpleCaptcha;

/**
 * contactus controller.
 *
 * @Route("/captcha")
 */
class CaptchaController extends Controller {

    /**
     * Contactus form.
     *
     * @Route("/", name="fe_captcha")
     * @Method("GET")
     */
    public function captcha() {

        $captcha = new SimpleCaptcha();

//        $captcha = new SimpleCaptcha();
        // Image generation
        $return = $captcha->CreateImage();
        $headers = array(
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline;');
        return new Response($return, 200, $headers);
    }

}
