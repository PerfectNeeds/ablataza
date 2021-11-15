<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Banner;

/**
 * Banner controller.
 *
 * @Route("/banner")
 */
class BannerController extends Controller {

    /**
     * Lists all Banner entities.
     *
     * @Route("/{placment}", name="fe_banner")
     * @Method("GET")
     * @Template()
     */
    public function BannerAction($placment) {
        $em = $this->getDoctrine()->getManager();

        $Banners = $em->getRepository('CMSBundle:Banner')->getRandBanner($placment, 3);
        return array(
            'Banners' => $Banners,
        );
    }

    /**
     * Lists all Banner entities.
     *
     * @Route("/one/{placment}", name="fe_banner_one")
     * @Method("GET")
     * @Template()
     */
    public function showOneBannerAction($placment) {
        $em = $this->getDoctrine()->getManager();

        $Banners = $em->getRepository('CMSBundle:Banner')->getRandBanner($placment, 1);
        return array(
            'Banners' => $Banners,
        );
    }

    /**
     * Lists all Banner entities.
     *
     * @Route("/list/{placment}", name="fe_banner_list")
     * @Method("GET")
     * @Template()
     */
    public function showListBannerAction($placment) {
        $em = $this->getDoctrine()->getManager();

        $Banners = $em->getRepository('CMSBundle:Banner')->getRandBanner($placment, 6);
        return array(
            'Banners' => $Banners,
        );
    }

}
