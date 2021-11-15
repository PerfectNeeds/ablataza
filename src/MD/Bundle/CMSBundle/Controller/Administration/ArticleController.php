<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\SuperCategory;
use MD\Bundle\CMSBundle\Entity\Article;
use MD\Bundle\CMSBundle\Form\ArticleType;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller {

    /**
     * Lists all Article entities.
     *
     * @Route("/", name="article")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Article')->findBy(array('deleted' => FALSE));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/", name="article_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Article:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Article();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('article/' . $seoEntity->getSlug());

        $em = $this->getDoctrine()->getManager();
        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($seoEntity, $form);

        if ($seoValidate) {
            $em->persist($seoEntity);
            $em->flush();

            $post = $this->getRequest()->request->get('post');
            $data = $this->getRequest()->request->get('data');

            if (isset($data['categories'])) {
                foreach ($data['categories'] as $category) {
                    $categoryEntity = $em->getRepository('CMSBundle:Category')->find($category);
                    $entity->addCategorie($categoryEntity);
                }
            }

            if (isset($data['superCategories'])) {
                foreach ($data['superCategories'] as $superCategory) {
                    $superCategoryEntity = $em->getRepository('CMSBundle:SuperCategory')->find($superCategory);
                    $entity->addSuperCategorie($superCategoryEntity);
                }
            }

            $postEntity = new Post();
            $content = array(
                'description' => $post['description'],
            );
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();

            $person = $em->getRepository('UserBundle:Person')->find(1);
            $entity->setPerson($person);

            $entity->setSeo($seoEntity);
            $entity->setPost($postEntity);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);

            return $this->redirect($this->generateUrl('article'));
        }

        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));
        $categories = $em->getRepository('CMSBundle:Category')->getAllCategoryBySuperCategoryType(SuperCategory::TYPE_ARTICLE);

        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
            'categories' => $categories,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a Article entity.
     *
     * @param Article $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Article $entity) {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('article_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Article entity.
     *
     * @Route("/new", name="article_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Article();
        $form = $this->createCreateForm($entity);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);

        $em = $this->getDoctrine()->getManager();
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));
        $categories = $em->getRepository('CMSBundle:Category')->getAllCategoryBySuperCategoryType(SuperCategory::TYPE_ARTICLE);

        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
            'categories' => $categories,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{id}/edit", name="article_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $editForm = $this->createEditForm($entity);
        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));
        $categories = $em->getRepository('CMSBundle:Category')->getAllCategoryBySuperCategoryType(SuperCategory::TYPE_ARTICLE);

        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
            'categories' => $categories,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Article entity.
     *
     * @param Article $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Article $entity) {
        $form = $this->createForm(new ArticleType(), $entity, array(
            'action' => $this->generateUrl('article_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Article entity.
     *
     * @Route("/{id}", name="article_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Article:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('article/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {
            $post = $this->getRequest()->request->get('post');
            $data = $this->getRequest()->request->get('data');


            $em->getRepository('CMSBundle:SuperCategory')->removeSuperCategoriesByArticleId($entity->getId());
            if (isset($data['superCategories'])) {
                foreach ($data['superCategories'] as $superCategory) {
                    $superCategoryEntity = $em->getRepository('CMSBundle:SuperCategory')->find($superCategory);
                    $entity->addSuperCategorie($superCategoryEntity);
                }
            }

            $em->getRepository('CMSBundle:Category')->removeCategoriesByArticleId($entity->getId());
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $category) {
                    $categoryEntity = $em->getRepository('CMSBundle:Category')->find($category);
                    $entity->addCategorie($categoryEntity);
                }
            }
            $postEntity = $entity->getPost();
            $content = array(
                'description' => $post['description'],
            );
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();


            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('article'));
        }
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));
        $categories = $em->getRepository('CMSBundle:Category')->getAllCategoryBySuperCategoryType(SuperCategory::TYPE_ARTICLE);
        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
            'categories' => $categories,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete", name="article_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Article')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('article'));
    }

    /**
     * Creates a form to delete a Article entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('article_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Lists all ArticleComment entities.
     *
     * @Route("/{id}", name="articlecomment")
     * @Method("GET")
     * @Template()
     */
    public function commentAction($id) {
        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository('CMSBundle:Article')->find($id);
        $entities = $em->getRepository('CMSBundle:ArticleComment')->findBy(array('article' => $id));

        return array(
            'entities' => $entities,
            'article' => $article,
        );
    }

    /**
     * Deletes a ArticleComment entity.
     *
     * @Route("/comment-delete", name="articlecomment_delete")
     * @Method("POST")
     */
    public function deleteCommentAction(Request $request) {
        $id = $this->getRequest()->request->get('id');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:ArticleComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticleComment entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('articlecomment', array('id' => $entity->getArticle()->getId())));
    }

    /**
     * Deletes a ArticleComment entity.
     *
     * @Route("/comment-publish", name="articlecomment_publish")
     * @Method("POST")
     */
    public function publishCommentAction() {
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:ArticleComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ArticleComment entity.');
        }

        if ($entity->getPublish() == TRUE) {
            $entity->setPublish(FALSE);
        } else {
            $entity->setPublish(TRUE);
        }

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('articlecomment', array('id' => $entity->getArticle()->getId())));
    }

}
