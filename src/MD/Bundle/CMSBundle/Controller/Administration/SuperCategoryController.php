<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\SuperCategory;
use MD\Bundle\CMSBundle\Form\SuperCategoryType;
use MD\Bundle\CMSBundle\Entity\Translation\SuperCategoryTranslation;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Entity\Translation\PostTranslation;
use MD\Bundle\CMSBundle\Entity\Translation\SeoTranslation;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * SuperCategory controller.
 *
 * @Route("/super-category")
 */
class SuperCategoryController extends Controller {

    /**
     * Lists all SuperCategory entities.
     *
     * @Route("/", name="supercategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:SuperCategory')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new SuperCategory entity.
     *
     * @Route("/", name="supercategory_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/SuperCategory:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new SuperCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('supercategory/' . $seoEntity->getSlug());

        $em = $this->getDoctrine()->getManager();
        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($seoEntity, $form);

        if ($seoValidate) {
            $em->persist($seoEntity);
            $em->flush();


            $entity->setSeo($seoEntity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('supercategory'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a SuperCategory entity.
     *
     * @param SuperCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SuperCategory $entity) {
        $form = $this->createForm(new SuperCategoryType(), $entity, array(
            'action' => $this->generateUrl('supercategory_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new SuperCategory entity.
     *
     * @Route("/new", name="supercategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new SuperCategory();
        $form = $this->createCreateForm($entity);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SuperCategory entity.
     *
     * @Route("/{id}/edit", name="supercategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SuperCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SuperCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());


        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to edit a SuperCategory entity.
     *
     * @param SuperCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SuperCategory $entity) {
        $form = $this->createForm(new SuperCategoryType(), $entity, array(
            'action' => $this->generateUrl('supercategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing SuperCategory entity.
     *
     * @Route("/{id}", name="supercategory_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/SuperCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SuperCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SuperCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($entity->getSeo() == NULL) {
            $seoEntity = new Seo();
            $seoEntity->setSlug('supercategory/' . $entity->getId());
            $em->persist($seoEntity);
            $em->flush();


            $entity->setSeo($seoEntity);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }
        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('supercategory/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('supercategory_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a SuperCategory entity.
     *
     * @Route("/delete", name="supercategory_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:SuperCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SuperCategory entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('supercategory'));
    }

    /**
     * Creates a form to delete a SuperCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('supercategory_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
