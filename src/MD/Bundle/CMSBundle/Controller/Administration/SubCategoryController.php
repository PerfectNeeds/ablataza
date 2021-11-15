<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\SubCategory;
use MD\Bundle\CMSBundle\Form\SubCategoryType;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * SubCategory controller.
 *
 * @Route("/sub-category")
 */
class SubCategoryController extends Controller {

    /**
     * Lists all SubCategory entities.
     *
     * @Route("/{pId}", name="subcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($pId) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:SubCategory')->findByCategory($pId);
        $category = $em->getRepository('CMSBundle:Category')->find($pId);

        return array(
            'entities' => $entities,
            'category' => $category,
        );
    }

    /**
     * Creates a new SubCategory entity.
     *
     * @Route("/{pId}", name="subcategory_create")
     * @Method("POST")
     * @Template("CMSBundle:SubCategory:new.html.twig")
     */
    public function createAction(Request $request, $pId) {
        $entity = new SubCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('subcategory/' . $seoEntity->getSlug());

        $em = $this->getDoctrine()->getManager();
        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($seoEntity, $form);

        if ($seoValidate) {

            $em->persist($seoEntity);
            $em->flush();

            $category = $em->getRepository('CMSBundle:Category')->find($pId);
            $entity->setCategory($category);

            $post = $this->getRequest()->request->get('post');
            $seo = $this->getRequest()->request->get('seo');
            $data = $this->getRequest()->request->get('data');

            $postEntity = new Post();
            $content = array("description" => $post['description'], 'brief' => $post['brief']);
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();


            $languages = \AppKernel::$subLang;

            foreach ($languages as $language) {

                $entityTranslation = new SubCategoryTranslation();
                $entityTranslation->setName($data['name_' . $language]);
                $entityTranslation->setLocale($language);
                $entityTranslation->setObject($entity);
                $entity->addTranslation($entityTranslation);

                //POST
                $postTranslation = new PostTranslation();
                $content = array("description" => $post['description' . ucfirst($language)], 'brief' => $post['brief_' . $language]);
                $postTranslation->setContent($content);
                $postTranslation->setLocale($language);
                $postTranslation->setObject($postEntity);
                $postEntity->addTranslation($postTranslation);

                // SEO
                $seoTranslation = new SeoTranslation();
                $seoTranslation->setTitle($seo['title_' . $language]);
                $seoTranslation->setMetaTag($seo['metaTag_' . $language]);
                $seoTranslation->setLocale($language);
                $seoTranslation->setObject($seoEntity);
                $seoEntity->addTranslation($seoTranslation);
            }


            $entity->setSeo($seoEntity);
            $entity->setPost($postEntity);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subcategory', array('pId' => $category->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a SubCategory entity.
     *
     * @param SubCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SubCategory $entity) {
        $form = $this->createForm(new SubCategoryType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new SubCategory entity.
     *
     * @Route("/new/{pId}", name="subcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($pId) {
        $entity = new SubCategory();
        $form = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('CMSBundle:Category')->find($pId);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);

        return array(
            'entity' => $entity,
            'category' => $category,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SubCategory entity.
     *
     * @Route("/{id}/edit", name="subcategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
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
     * Creates a form to edit a SubCategory entity.
     *
     * @param SubCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(SubCategory $entity) {
        $form = $this->createForm(new SubCategoryType(), $entity, array(
            'action' => $this->generateUrl('subcategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing SubCategory entity.
     *
     * @Route("/{id}", name="subcategory_update")
     * @Method("PUT")
     * @Template("CMSBundle:SubCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($entity->getSeo() == NULL) {
            $seoEntity = new Seo();
            $seoEntity->setSlug('subcategory/' . $entity->getId());
            $em->persist($seoEntity);
            $em->flush();


            $entity->setSeo($seoEntity);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }

        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('subcategory/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('subcategory_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a SubCategory entity.
     *
     * @Route("/delete/{pId}", name="subcategory_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $pId) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:SubCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SubCategory entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('subcategory', array('pId' => $pId)));
    }

    /**
     * Creates a form to delete a SubCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
