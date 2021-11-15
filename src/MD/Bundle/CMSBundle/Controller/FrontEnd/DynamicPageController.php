<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\DynamicPage;

/**
 * DynamicPage controller.
 *
 * @Route("/page")
 */
class DynamicPageController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/{name}", name="fe_dynamicpage")
     * @Method("GET")
     * @Template()
     */
    public function aboutAction($name) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->findOneBySlug($name);

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans("Home"), $this->get("router")->generate("fe_home"));
        $breadcrumbs->addItem($page->getTitle());

        $mission = $em->getRepository('CMSBundle:DynamicPage')->find(4);
        $vision = $em->getRepository('CMSBundle:DynamicPage')->find(5);


        return array(
            'page' => $page,
            'mission' => $mission,
            'vision' => $vision,
        );
    }

}
