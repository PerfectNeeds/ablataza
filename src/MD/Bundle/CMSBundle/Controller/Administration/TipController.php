<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Tip;
use MD\Bundle\CMSBundle\Form\TipType;

/**
 * Tip controller.
 *
 * @Route("/tip")
 */
class TipController extends Controller {

    /**
     * Lists all Tip entities.
     *
     * @Route("/", name="tip")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Tip')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Tip entity.
     *
     * @Route("/", name="tip_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Tip:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Tip();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $person = $em->getRepository('UserBundle:Person')->find(1);
            $entity->setPerson($person);

            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('tip'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Tip entity.
     *
     * @param Tip $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tip $entity) {
        $form = $this->createForm(new TipType(), $entity, array(
            'action' => $this->generateUrl('tip_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Tip entity.
     *
     * @Route("/new", name="tip_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Tip();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tip entity.
     *
     * @Route("/{id}/edit", name="tip_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Tip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Tip entity.
     *
     * @param Tip $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Tip $entity) {
        $form = $this->createForm(new TipType(), $entity, array(
            'action' => $this->generateUrl('tip_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Tip entity.
     *
     * @Route("/{id}", name="tip_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Tip:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Tip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tip'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Tip entity.
     *
     * @Route("/delete", name="tip_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Tip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('tip'));
    }

    /**
     * Creates a form to delete a Tip entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('tip_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
