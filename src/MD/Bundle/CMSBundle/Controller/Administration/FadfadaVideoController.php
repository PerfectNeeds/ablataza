<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\FadfadaVideo;
use MD\Bundle\CMSBundle\Form\FadfadaVideoType;

/**
 * FadfadaVideo controller.
 *
 * @Route("/fadfada-video")
 */
class FadfadaVideoController extends Controller {

    /**
     * Lists all FadfadaVideo entities.
     *
     * @Route("/", name="fadfadavideo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:FadfadaVideo')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new FadfadaVideo entity.
     *
     * @Route("/create", name="fadfadavideo_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/FadfadaVideo:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FadfadaVideo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fadfadavideo'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FadfadaVideo entity.
     *
     * @param FadfadaVideo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FadfadaVideo $entity) {
        $form = $this->createForm(new FadfadaVideoType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new FadfadaVideo entity.
     *
     * @Route("/new", name="fadfadavideo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new FadfadaVideo();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FadfadaVideo entity.
     *
     * @Route("/{id}/edit", name="fadfadavideo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:FadfadaVideo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FadfadaVideo entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a FadfadaVideo entity.
     *
     * @param FadfadaVideo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FadfadaVideo $entity) {
        $form = $this->createForm(new FadfadaVideoType(), $entity, array(
            'action' => $this->generateUrl('fadfadavideo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing FadfadaVideo entity.
     *
     * @Route("/{id}", name="fadfadavideo_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/FadfadaVideo:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:FadfadaVideo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FadfadaVideo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fadfadavideo_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a FadfadaVideo entity.
     *
     * @Route("/delete", name="fadfadavideo_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:FadfadaVideo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FadfadaVideo entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('fadfadavideo'));
    }

    /**
     * Creates a form to delete a FadfadaVideo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('fadfadavideo_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
