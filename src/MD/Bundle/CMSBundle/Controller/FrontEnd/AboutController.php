<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\DynamicPage;

/**
 * About controller.
 *
 * @Route("")
 */
class AboutController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/certificateandaward", name="fe_certificate")
     * @Method("GET")
     * @Template()
     */
    public function certificateAndAwardAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(2);

        return array(
            'page' => $page,
        );
    }
    
    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/ourvalues", name="fe_values")
     * @Method("GET")
     * @Template()
     */
    public function ourValuesAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(3);
        $mission = $em->getRepository('CMSBundle:DynamicPage')->find(4);
        $vision = $em->getRepository('CMSBundle:DynamicPage')->find(5);

        return array(
            'page' => $page,
            'mission' => $vision,
            'vision' => $vision,
        );
    }
    
    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/history", name="fe_history")
     * @Method("GET")
     * @Template()
     */
    public function historyAction() {
        $em = $this->getDoctrine()->getManager();
        $history = $em->getRepository('CMSBundle:DynamicPage')->find(14);
        $egypt = $em->getRepository('CMSBundle:DynamicPage')->find(15);
        $world = $em->getRepository('CMSBundle:DynamicPage')->find(16);

        return array(
            'history' => $history,
            'egypt' => $egypt,
            'world' => $world,
        );
    }

}
