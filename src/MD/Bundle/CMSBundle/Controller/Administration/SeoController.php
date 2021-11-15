<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Service;
use MD\Bundle\CMSBundle\Form\ServiceType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormError;
use MD\Utils\Validate;
use Symfony\Component\HttpFoundation\Session\Session;

class SeoController extends Controller {

    protected $em = null;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function validateSeo(\MD\Bundle\CMSBundle\Entity\Seo $seo, $seoForm) {
        $session = new Session();
        $return = TRUE;
        if ($seo->getId() == NULL) {
            $checkSlug = $this->em->getRepository('CMSBundle:Seo')->findBy(array('slug' => $seo->getRawSlug()));
            if (count($checkSlug) > 0) {
                $session->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                $return = FALSE;
            }
        } else {
            $checkSlug = $this->em->getRepository('CMSBundle:Seo')->queryBySlugAndNotId($seo->getRawSlug(), $seo->getId());
            if (count($checkSlug) > 0 and is_array($checkSlug)) {
                $session->getFlashBag()->add('error', 'the HTML Slug is duplicated, please change it');
                $return = FALSE;
            }
        }
        return $return;
    }

}
