<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\SiteSetting;
use MD\Bundle\CMSBundle\Form\SiteSettingType;
use MD\Bundle\CMSBundle\Entity\Translation\SiteSettingTranslation;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Entity\Translation\PostTranslation;
use MD\Bundle\CMSBundle\Entity\Translation\SeoTranslation;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * SiteSetting controller.
 *
 * @Route("/site-setting")
 */
class SiteSettingController extends Controller {

    /**
     * Lists all SiteSetting entities.
     *
     * @Route("/", name="sitesetting")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:SiteSetting')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Displays a form to edit an existing SiteSetting entity.
     *
     * @Route("/{id}/edit", name="sitesetting_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SiteSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSetting entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a SiteSetting entity.
     *
     * @param SiteSetting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SiteSetting $entity) {
        $form = $this->createForm(new SiteSettingType(), $entity, array(
            'action' => $this->generateUrl('sitesetting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing SiteSetting entity.
     *
     * @Route("/{id}", name="sitesetting_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/SiteSetting:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SiteSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteSetting entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('sitesetting_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

}
