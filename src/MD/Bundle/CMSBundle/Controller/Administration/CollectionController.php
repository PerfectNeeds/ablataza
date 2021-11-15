<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Collection;
use MD\Bundle\CMSBundle\Form\CollectionType;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * Collection controller.
 *
 * @Route("/category")
 */
class CollectionController extends Controller {

    /**
     * Lists all Collection entities.
     *
     * @Route("/", name="collection")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Collection')->findBy(array('deleted' => FALSE));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Collection entity.
     *
     * @Route("/", name="collection_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Collection:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Collection();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('collection/' . $seoEntity->getSlug());

        $em = $this->getDoctrine()->getManager();
        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($seoEntity, $form);

        if ($seoValidate) {
            $em->persist($seoEntity);
            $em->flush();

            $post = $this->getRequest()->request->get('post');

            $postEntity = new Post();
            $content = array("description" => $post['description']);
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();

            $entity->setPost($postEntity);
            $entity->setSeo($seoEntity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('collection'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a Collection entity.
     *
     * @param Collection $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Collection $entity) {
        $form = $this->createForm(new CollectionType(), $entity, array(
            'action' => $this->generateUrl('collection_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Collection entity.
     *
     * @Route("/new", name="collection_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Collection();
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
     * Displays a form to edit an existing Collection entity.
     *
     * @Route("/{id}/edit", name="collection_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
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
     * Creates a form to edit a Collection entity.
     *
     * @param Collection $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Collection $entity) {
        $form = $this->createForm(new CollectionType(), $entity, array(
            'action' => $this->generateUrl('collection_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Collection entity.
     *
     * @Route("/{id}", name="collection_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Collection:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('collection/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {
            $post = $this->getRequest()->request->get('post');


            $postEntity = $entity->getPost();
            $content = array("description" => $post['description']);
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('collection_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a Collection entity.
     *
     * @Route("/delete", name="collection_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Collection')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Collection entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('collection'));
    }

    /**
     * Creates a form to delete a Collection entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('collection_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
