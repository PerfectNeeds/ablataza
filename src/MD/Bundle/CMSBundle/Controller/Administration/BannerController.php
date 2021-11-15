<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Banner;
use MD\Bundle\CMSBundle\Form\BannerType;
use MD\Bundle\MediaBundle\Controller\ImageController;

/**
 * Banner controller.
 *
 * @Route("/banner")
 */
class BannerController extends Controller {

    /**
     * Lists all Banner entities.
     *
     * @Route("/", name="banner")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('CMSBundle:Banner')->findAll();
        $entities = $em->getRepository('CMSBundle:Banner')->findAll();
        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Banner entity.
     *
     * @Route("/", name="banner_create")
     * @Method("POST")
     * @Template("CMSBundle:Banner:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Banner();
        $form = $this->createForm(new BannerType(), $entity);

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];
        $form->bind($request);
        if ($this->getRequest()->request->get('open_type') != NULL) {
            $openType = $this->getRequest()->request->get('open_type');
        } else {
            $openType = 0;
        }
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
//            $entity->setPlacement(1); // HomePage
            $entity->setOpenType($openType);

            $em->persist($entity);
            $em->flush();

            // Upload Image
            $imageController = new ImageController();
            $imageController->uploadSingleImage($em, $entity, $file, 2);

            return $this->redirect($this->generateUrl('banner'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Displays a form to create a new Banner entity.
     *
     * @Route("/new", name="banner_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Banner();
        $form = $this->createForm(new BannerType(), $entity);
        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Displays a form to edit an existing Banner entity.
     *
     * @Route("/{id}/edit", name="banner_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Banner')->find($id);
        $image = $entity->getImage();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $bannerType = new BannerType();
        $bannerType->placmentData = $entity->getPlacement();

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $editForm = $this->createForm($bannerType, $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'upload_form' => $formView,
            'image' => $image,
        );
    }

    /**
     * Edits an existing Banner entity.
     *
     * @Route("/{id}", name="banner_update")
     * @Method("PUT")
     * @Template("CMSBundle:Banner:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }
        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new BannerType(), $entity);
        $editForm->bind($request);
        if ($this->getRequest()->request->get('open_type') != NULL) {
            $openType = $this->getRequest()->request->get('open_type');
        } else
            $openType = 0;
        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setOpenType($openType);

            // Upload Image
            $imageController = new ImageController();
            $imageController->uploadSingleImage($em, $entity, $file, 2);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('banner_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'upload_form' => $formView,
        );
    }

    /**
     * Deletes a Banner entity.
     *
     * @Route("/delete", name="banner_delete")
     * @Method("POST")
     */
    public function deleteAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('banner'));
    }

    /**
     * Creates a form to delete a Banner entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
