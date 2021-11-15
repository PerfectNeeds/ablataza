<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\DynamicPage;

/**
 * WhatWeDo controller.
 *
 * @Route("")
 */
class WhatWeDoController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/agriculture", name="fe_agriculture")
     * @Method("GET")
     * @Template()
     */
    public function agricultureAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(6);

        return array(
            'page' => $page,
        );
    }
    
    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/environment", name="fe_environment")
     * @Method("GET")
     * @Template()
     */
    public function environmentAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(7);

        return array(
            'page' => $page,
        );
    }
    
    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/manufacturing", name="fe_manufacturing")
     * @Method("GET")
     * @Template()
     */
    public function manufacturingAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(8);

        return array(
            'page' => $page,
        );
    }
    
    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/quality", name="fe_quality")
     * @Method("GET")
     * @Template()
     */
    public function qualityAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(9);

        return array(
            'page' => $page,
        );
    }
    
}
