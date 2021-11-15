<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Fadfada;
use MD\Bundle\CMSBundle\Form\FadfadaType;

/**
 * Fadfada controller.
 *
 * @Route("/fadfada")
 */
class FadfadaController extends Controller {

    /**
     * Lists all Fadfada entities.
     *
     * @Route("/", name="fadfada")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Fadfada')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Fadfada entity.
     *
     * @Route("/create", name="fadfada_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Fadfada:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Fadfada();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fadfada'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Fadfada entity.
     *
     * @param Fadfada $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Fadfada $entity) {
        $form = $this->createForm(new FadfadaType(), $entity, array(
            'action' => $this->generateUrl('fadfada_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Fadfada entity.
     *
     * @Route("/new", name="fadfada_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Fadfada();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Fadfada entity.
     *
     * @Route("/{id}/edit", name="fadfada_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Fadfada')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fadfada entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Fadfada entity.
     *
     * @param Fadfada $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Fadfada $entity) {
        $form = $this->createForm(new FadfadaType(), $entity, array(
            'action' => $this->generateUrl('fadfada_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Fadfada entity.
     *
     * @Route("/{id}", name="fadfada_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Fadfada:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Fadfada')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fadfada entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('fadfada'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Fadfada entity.
     *
     * @Route("/delete", name="fadfada_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Fadfada')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fadfada entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('fadfada'));
    }

    /**
     * Creates a form to delete a Fadfada entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('fadfada_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Lists all FadfadaComment entities.
     *
     * @Route("/{id}", name="fadfadacomment")
     * @Method("GET")
     * @Template()
     */
    public function commentAction($id) {
        $em = $this->getDoctrine()->getManager();

        $fadfada = $em->getRepository('CMSBundle:Fadfada')->find($id);
        $entities = $em->getRepository('CMSBundle:FadfadaComment')->findBy(array('fadfada' => $id));

        return array(
            'entities' => $entities,
            'fadfada' => $fadfada,
        );
    }

    /**
     * Deletes a FadfadaComment entity.
     *
     * @Route("/comment-delete", name="fadfadacomment_delete")
     * @Method("POST")
     */
    public function deleteCommentAction(Request $request) {
        $id = $this->getRequest()->request->get('id');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:FadfadaComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FadfadaComment entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('fadfadacomment', array('id' => $entity->getFadfada()->getId())));
    }

    /**
     * Deletes a FadfadaComment entity.
     *
     * @Route("/comment-publish", name="fadfadacomment_publish")
     * @Method("POST")
     */
    public function publishCommentAction() {
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:FadfadaComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FadfadaComment entity.');
        }

        if ($entity->getPublish() == TRUE) {
            $entity->setPublish(FALSE);
        } else {
            $entity->setPublish(TRUE);
        }

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('fadfadacomment', array('id' => $entity->getFadfada()->getId())));
    }

}
