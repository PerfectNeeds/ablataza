<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Category;
use MD\Bundle\CMSBundle\Form\CategoryType;
use MD\Bundle\CMSBundle\Entity\Translation\CategoryTranslation;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Entity\Translation\PostTranslation;
use MD\Bundle\CMSBundle\Entity\Translation\SeoTranslation;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller {

    /**
     * Lists all Category entities.
     *
     * @Route("/{pId}", name="category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($pId) {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Category')->findBySuperCategory($pId);
        $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($pId);


        return array(
            'entities' => $entities,
            'superCategory' => $superCategory,
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/{pId}", name="category_create")
     * @Method("POST")
     * @Template("CMSBundle:Category:new.html.twig")
     */
    public function createAction(Request $request, $pId) {
        $entity = new Category();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('category/' . $seoEntity->getSlug());

        $em = $this->getDoctrine()->getManager();
        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($seoEntity, $form);

        if ($seoValidate) {

            $em->persist($seoEntity);
            $em->flush();

            $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($pId);
            $entity->setSuperCategory($superCategory);

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

                $entityTranslation = new CategoryTranslation();
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

            return $this->redirect($this->generateUrl('category', array('pId' => $superCategory->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity) {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Category entity.
     *
     * @Route("/new/{pId}", name="category_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($pId) {
        $entity = new Category();
        $form = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager();
        $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($pId);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);

        return array(
            'entity' => $entity,
            'superCategory' => $superCategory,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
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
     * Creates a form to edit a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Category $entity) {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Category entity.
     *
     * @Route("/{id}", name="category_update")
     * @Method("PUT")
     * @Template("CMSBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($entity->getSeo() == NULL) {
            $seoEntity = new Seo();
            $seoEntity->setSlug('category/' . $entity->getId());
            $em->persist($seoEntity);
            $em->flush();


            $entity->setSeo($seoEntity);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }

        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('category/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/delete/{pId}", name="category_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $pId) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('category', array('pId' => $pId)));
    }

    /**
     * Creates a form to delete a Category entity by id.
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
