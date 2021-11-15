<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\SuperCategory;
use MD\Bundle\CMSBundle\Entity\Recipe;
use MD\Bundle\CMSBundle\Form\RecipeType;
use MD\Bundle\CMSBundle\Entity\Post;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Form\SeoType;

/**
 * Recipe controller.
 *
 * @Route("/recipe")
 */
class RecipeController extends Controller {

    /**
     * Lists all Recipe entities.
     *
     * @Route("/", name="recipe")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:Recipe')->findBy(array('deleted' => FALSE));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Recipe entity.
     *
     * @Route("/", name="recipe_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/Recipe:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Recipe();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);
        $seoForm->bind($request);
        $seoEntity->setSlug('recipe/' . $seoEntity->getSlug());

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

            if (isset($data['subCategories'])) {
                foreach ($data['subCategories'] as $subCategory) {
                    $subCategoryEntity = $em->getRepository('CMSBundle:SubCategory')->find($subCategory);
                    $entity->addSubCategorie($subCategoryEntity);
                }
            }

            $postEntity = new Post();
            $content = array(
                'ingredients' => $post['ingredients'],
                'recipe' => $post['recipe'],
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

            return $this->redirect($this->generateUrl('recipe'));
        }

        $supperCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_RECIPE, 'deleted' => FALSE));
        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);

        return array(
            'entity' => $entity,
            'supperCategories' => $supperCategories,
            'subCategories' => $subCategories,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to create a Recipe entity.
     *
     * @param Recipe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Recipe $entity) {
        $form = $this->createForm(new RecipeType(), $entity, array(
            'action' => $this->generateUrl('recipe_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Recipe();
        $form = $this->createCreateForm($entity);

        $seoEntity = new Seo();
        $seoForm = $this->createForm(new SeoType(), $seoEntity);

        $em = $this->getDoctrine()->getManager();
        $supperCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_RECIPE, 'deleted' => FALSE));
        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);

        return array(
            'entity' => $entity,
            'supperCategories' => $supperCategories,
            'subCategories' => $subCategories,
            'form' => $form->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $editForm = $this->createEditForm($entity);
        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $supperCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_RECIPE, 'deleted' => FALSE));
        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);

        return array(
            'entity' => $entity,
            'supperCategories' => $supperCategories,
            'subCategories' => $subCategories,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Recipe entity.
     *
     * @param Recipe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Recipe $entity) {
        $form = $this->createForm(new RecipeType(), $entity, array(
            'action' => $this->generateUrl('recipe_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing Recipe entity.
     *
     * @Route("/{id}", name="recipe_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/Recipe:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $seoForm = $this->createForm(new SeoType(), $entity->getSeo());
        $seoForm->bind($request);
        $entity->getSeo()->setSlug('recipe/' . $entity->getSeo()->getSlug());

        $seoController = new SeoController($em);
        $seoValidate = $seoController->validateSeo($entity->getSeo(), $editForm);

        if ($seoValidate) {
            $post = $this->getRequest()->request->get('post');
            $data = $this->getRequest()->request->get('data');

            $em->getRepository('CMSBundle:Category')->removeCategoriesByRecipeId($entity->getId());
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $category) {
                    $categoryEntity = $em->getRepository('CMSBundle:Category')->find($category);
                    $entity->addCategorie($categoryEntity);
                }
            }

            $em->getRepository('CMSBundle:SubCategory')->removeSubCategoriesByRecipeId($entity->getId());
            if (isset($data['subCategories'])) {
                foreach ($data['subCategories'] as $subCategory) {
                    $subCategoryEntity = $em->getRepository('CMSBundle:SubCategory')->find($subCategory);
                    $entity->addSubCategorie($subCategoryEntity);
                }
            }

            $postEntity = $entity->getPost();
            $content = array(
                'ingredients' => $post['ingredients'],
                'recipe' => $post['recipe'],
            );
            $postEntity->setContent($content);
            $em->persist($postEntity);
            $em->flush();


            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('recipe'));
        }
        $supperCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_RECIPE, 'deleted' => FALSE));
        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);
        return array(
            'entity' => $entity,
            'supperCategories' => $supperCategories,
            'subCategories' => $subCategories,
            'edit_form' => $editForm->createView(),
            'formSeo' => $seoForm->createView(),
        );
    }

    /**
     * Deletes a Recipe entity.
     *
     * @Route("/delete", name="recipe_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('recipe'));
    }

    /**
     * Creates a form to delete a Recipe entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('recipe_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Lists all RecipeComment entities.
     *
     * @Route("/{id}", name="recipecomment")
     * @Method("GET")
     * @Template()
     */
    public function commentAction($id) {
        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('CMSBundle:Recipe')->find($id);
        $entities = $em->getRepository('CMSBundle:RecipeComment')->findBy(array('recipe' => $id));

        return array(
            'entities' => $entities,
            'recipe' => $recipe,
        );
    }

    /**
     * Deletes a RecipeComment entity.
     *
     * @Route("/comment-delete", name="recipecomment_delete")
     * @Method("POST")
     */
    public function deleteCommentAction(Request $request) {
        $id = $this->getRequest()->request->get('id');
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:RecipeComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RecipeComment entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('recipecomment', array('id' => $entity->getRecipe()->getId())));
    }

    /**
     * Deletes a RecipeComment entity.
     *
     * @Route("/comment-publish", name="recipecomment_publish")
     * @Method("POST")
     */
    public function publishCommentAction() {
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:RecipeComment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RecipeComment entity.');
        }

        if ($entity->getPublish() == TRUE) {
            $entity->setPublish(FALSE);
        } else {
            $entity->setPublish(TRUE);
        }

        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('recipecomment', array('id' => $entity->getRecipe()->getId())));
    }

}
