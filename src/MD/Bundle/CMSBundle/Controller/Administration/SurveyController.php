<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Survey;
use MD\Bundle\CMSBundle\Form\SurveyType;
use MD\Bundle\MediaBundle\Controller\ImageController;

/**
 * Survey controller.
 *
 * @Route("/survey")
 */
class SurveyController extends Controller {

    /**
     * Lists all Survey entities.
     *
     * @Route("/", name="survey")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Survey')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Survey entity.
     *
     * @Route("/", name="survey_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Survey:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Survey();
        $form = $this->createForm(new SurveyType(), $entity);

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
            $imageController->uploadSingleImage($em, $entity, $file, 6);

            return $this->redirect($this->generateUrl('survey'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Creates a form to create a Survey entity.
     *
     * @param Survey $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Survey $entity) {
        $form = $this->createForm(new SurveyType(), $entity, array(
            'action' => $this->generateUrl('survey_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Survey entity.
     *
     * @Route("/new", name="survey_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Survey();
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
     * Displays a form to edit an existing Survey entity.
     *
     * @Route("/{id}/edit", name="survey_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }

        $image = $entity->getImage();

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'upload_form' => $formView,
            'image' => $image,
        );
    }

    /**
     * Creates a form to edit a Survey entity.
     *
     * @param Survey $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Survey $entity) {
        $form = $this->createForm(new SurveyType(), $entity, array(
            'action' => $this->generateUrl('survey_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Survey entity.
     *
     * @Route("/{id}", name="survey_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Survey:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // Upload Image
            $imageController = new ImageController();
            $imageController->uploadSingleImage($em, $entity, $file, 6);

            $em->persist($entity);
            $em->flush();


            return $this->redirect($this->generateUrl('survey_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Survey entity.
     *
     * @Route("/delete", name="survey_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }

        $imageId = $entity->getId();
        $oldImage = $entity->getImage();
        if ($oldImage) {
            $oldImage->storeFilenameForRemove('survey/' . $imageId);
            $oldImage->removeUpload();
            $em->remove($oldImage);
            $em->persist($oldImage);
            $em->persist($entity);
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('survey'));
    }

    /**
     * Creates a form to delete a Survey entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('survey_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
