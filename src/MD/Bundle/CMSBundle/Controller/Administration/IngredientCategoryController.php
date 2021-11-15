<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\IngredientCategory;
use MD\Bundle\CMSBundle\Form\IngredientCategoryType;
use MD\Bundle\MediaBundle\Controller\ImageController;

/**
 * IngredientCategory controller.
 *
 * @Route("/ingredient-category")
 */
class IngredientCategoryController extends Controller {

    /**
     * Lists all IngredientCategory entities.
     *
     * @Route("/", name="ingredientcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:IngredientCategory')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new IngredientCategory entity.
     *
     * @Route("/create", name="ingredientcategory_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/IngredientCategory:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new IngredientCategory();
        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Upload Image
            $imageController = new ImageController();
            $imageController->uploadSingleImage($em, $entity, $file, 7);

            return $this->redirect($this->generateUrl('ingredientcategory'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a IngredientCategory entity.
     *
     * @param IngredientCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(IngredientCategory $entity) {
        $form = $this->createForm(new IngredientCategoryType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new IngredientCategory entity.
     *
     * @Route("/new", name="ingredientcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new IngredientCategory();
        $form = $this->createCreateForm($entity);

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Displays a form to edit an existing IngredientCategory entity.
     *
     * @Route("/{id}/edit", name="ingredientcategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:IngredientCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IngredientCategory entity.');
        }

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Creates a form to edit a IngredientCategory entity.
     *
     * @param IngredientCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(IngredientCategory $entity) {
        $form = $this->createForm(new IngredientCategoryType(), $entity, array(
            'action' => $this->generateUrl('ingredientcategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing IngredientCategory entity.
     *
     * @Route("/{id}", name="ingredientcategory_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/IngredientCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:IngredientCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IngredientCategory entity.');
        }

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            // Upload Image
            $imageController = new ImageController();
            $imageController->uploadSingleImage($em, $entity, $file, 7);

            return $this->redirect($this->generateUrl('ingredientcategory_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a IngredientCategory entity.
     *
     * @Route("/delete", name="ingredientcategory_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:IngredientCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find IngredientCategory entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('ingredientcategory'));
    }

    /**
     * Creates a form to delete a IngredientCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('ingredientcategory_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
