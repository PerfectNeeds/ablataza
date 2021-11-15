<?php

namespace MD\Bundle\UserBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MD\Utils\Validate as V;
use \MD\Bundle\MediaBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Entity\SuperCategory;
use MD\Bundle\CMSBundle\Entity\Recipe;

/**
 * Recipe controller.
 * @Route("/recipe")
 */
class RecipeController extends Controller {

    /**
     * Create a new Account entity.
     * @Route("/profile/{slug}/recipe/{page}", requirements={"page" = "\d+"}, name="fe_show_profile_recipe")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Recipe:viewProfileRecipe.html.twig")
     */
    public function viewProfileRecipeAction($slug, $page = 1) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);

        $search = new \stdClass;
        $search->userId = $person->getId();
        $search->draft = 0;
        $search->publish = 1;

        $recipeCount = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($recipeCount, $page, 9);
        $recipes = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId(), 4);

        return array(
            'person' => $person,
            'recipeCount' => $recipeCount,
            'recipes' => $recipes,
            'paginator' => $paginator->getPagination(),
            'followers' => $followers,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/my-recipe/{page}",requirements={"page" = "\d+"},  name="fe_my_recipe")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Recipe:myRecipe.html.twig")
     */
    public function myRecipeAction($page = 1) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();
        $search = new \stdClass;
        $search->userId = $this->get('security.context')->getToken()->getUser()->getPerson()->getId();
        $search->draft = 1;
        $search->publish = 1;
        $count = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page);
        $entities = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        return array(
            "entities" => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/add-recipe", name="fe_add_recipe")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Recipe:addRecipe.html.twig")
     */
    public function addRecipeAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();
        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);
        $form = $this->createForm(new \MD\Bundle\UserBundle\Form\PersonType());
        return array(
            'subCategories' => $subCategories,
            'form' => $form->createView(),
        );
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete-recipe", name="fe_recipe_delete")
     * @Method("POST")
     */
    public function deleteRecipeAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Recipe')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            throw $this->createNotFoundException('Access Denied.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم الحذف بنجاح');
        return $this->redirect($this->generateUrl('fe_my_recipe'));
    }

    /**
     * Creates a new Recipe entity.
     *
     * @Route("/create-recipe", name="fe_recipe_create")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Recipe:addRecipe.html.twig")
     */
    public function createRecipeAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $entity = new Recipe();

        $em = $this->getDoctrine()->getManager();
        $post = $this->getRequest()->request->get('post');
        $data = $this->getRequest()->request->get('data');
        $action = $this->getRequest()->request->get('action');

        $return = TRUE;
        $error = array();
        if (!V::not_null($data['title'])) {
            array_push($error, "أسم الوصفة");
            $return = FALSE;
        }
        if (!V::not_null($data['subCategory'])) {
            array_push($error, "التصنيف");
            $return = FALSE;
        }
        if (!V::not_null($post['ingredients'])) {
            array_push($error, "المقادير");
            $return = FALSE;
        }
        if (!V::not_null($post['recipe'])) {
            array_push($error, "طريقة عمل الوصفة");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = 'يجب عليك إدخال ';
            for ($i = 0; $i < count($error); $i++) {
                if (count($error) == $i + 1) {
                    $return .= $error[$i];
                } else {
                    if (count($error) == $i + 2) {
                        $return .= $error[$i] . ' و ';
                    } else {
                        $return .= $error[$i] . ', ';
                    }
                }
            }
            $session = new Session();
            $session->getFlashBag()->add('error', $return);

            $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);
            $form = $this->createForm(new \MD\Bundle\UserBundle\Form\PersonType());

            return $this->render("UserBundle:FrontEnd/Recipe:addRecipe.html.twig", array(
                        'subCategories' => $subCategories,
                        'form' => $form->createView(),
                        'post' => $post,
                        'data' => $data
            ));
        }

        if ($action == "draft") {
            $entity->setDraft(TRUE);
        } else {
            $entity->setDraft(FALSE);
        }

        $entity->setTitle($data['title']);
        if (V::not_null($data['preparationTime'])) {
            $entity->setPreparationTime($data['preparationTime']);
        }
        if (V::not_null($data['peopleNo'])) {
            $entity->setPeopleNo($data['peopleNo']);
        }
        if (V::not_null($data['cookingTime'])) {
            $entity->setCookingTime($data['cookingTime']);
        }
        $entity->setPublish(False);

        $newCategoryEntity = $em->getRepository('CMSBundle:Category')->find(3);
        $entity->addCategorie($newCategoryEntity);
        if (isset($data['subCategory'])) {
            $subCategoryEntity = $em->getRepository('CMSBundle:SubCategory')->find($data['subCategory']);
            $entity->addSubCategorie($subCategoryEntity);
        }

        $postEntity = new \MD\Bundle\CMSBundle\Entity\Post();
        $content = array(
            'ingredients' => $post['ingredients'],
            'recipe' => $post['recipe'],
        );
        $postEntity->setContent($content);
        $em->persist($postEntity);
        $em->flush();

        $person = $em->getRepository('UserBundle:Person')->find($this->getUser()->getPerson()->getId());
        $entity->setPerson($person);
        $em->persist($entity);
        $em->flush();

        $seoEntity = new \MD\Bundle\CMSBundle\Entity\Seo();
        $seoEntity->setSlug('recipe/' . $entity->getId() . '-' . $entity->getTitle());
        $em->persist($seoEntity);
        $em->flush();

        $entity->setSeo($seoEntity);
        $entity->setPost($postEntity);
        $em->persist($entity);
        $em->flush();
        $em->refresh($entity);

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType('files'));
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $files = $data_upload["files"];

        foreach ($files as $file) {

            if ($file != null) {
                $uploadPath = 'recipe/image/';
                $imageId = $entity->getPost()->getId();
                $mainImage = $entity->getPost()->getMainImage();
                $image = new Image();

                if ($mainImage) {
                    $image->setImageType(Image::TYPE_GALLERY);
                } else {
                    $image->setImageType(Image::TYPE_MAIN);
                }
                $em->persist($image);
                $em->flush();
                $image->setFile($file);
                $image->preUpload();
                $image->upload($uploadPath . $imageId);
                $entity->getPost()->addImage($image);
                $em->persist($entity);
                $em->flush();
            }
        }

//        $imageController = new \MD\Bundle\MediaBundle\Controller\ImageController();
//        $imageController->uploadSingleImage($em, $entity, $file, 4);

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'شكرا  <br>وصفتك وصلت لأبلة طازة وهتنزلها لحبايبها بعد مراجعتها <br> شاركينا أكتر بوصفاتك.');

        return $this->redirect($this->generateUrl('fe_my_recipe'));
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{slug}/edit-recipe", name="fe_recipe_edit")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Recipe:editRecipe.html.twig")
     */
    public function editRecipeAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $subCategories = $em->getRepository('CMSBundle:SubCategory')->getAllSubCategoryBySuperCategoryType(SuperCategory::TYPE_RECIPE);

        return array(
            'entity' => $entity,
            'subCategories' => $subCategories,
        );
    }

    /**
     * Edits an existing Recipe entity.
     *
     * @Route("/update-recipe/{slug}", name="fe_recipe_update")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Recipe:editRecipe.html.twig")
     */
    public function updateRecipeAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $post = $this->getRequest()->request->get('post');
        $data = $this->getRequest()->request->get('data');
        $action = $this->getRequest()->request->get('action');
        $return = TRUE;
        $error = array();
        if (!V::not_null($data['title'])) {
            array_push($error, "Title");
            $return = FALSE;
        }
        if (count($data['subCategories']) == 0) {
            array_push($error, "Category");
            $return = FALSE;
        }
        if (!V::not_null($post['ingredients'])) {
            array_push($error, "ingredients");
            $return = FALSE;
        }
        if (!V::not_null($post['recipe'])) {
            array_push($error, "recipe");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = 'You must enter ';
            for ($i = 0; $i < count($error); $i++) {
                if (count($error) == $i + 1) {
                    $return .= $error[$i];
                } else {
                    if (count($error) == $i + 2) {
                        $return .= $error[$i] . ' and ';
                    } else {
                        $return .= $error[$i] . ', ';
                    }
                }
            }
            $session = new Session();
            $session->getFlashBag()->add('error', $return);

            return $this->redirect($this->generateUrl('fe_recipe_edit', array('slug' => $entity->getSeo()->getSlug())));
        }

        if (isset($data['draft']) AND $data['draft'] == "1") {
            $entity->setDraft(TRUE);
        } else {
            $entity->setDraft(FALSE);
        }
        $entity->setTitle($data['title']);
        if (V::not_null($data['preparationTime'])) {
            $entity->setPreparationTime($data['preparationTime']);
        }
        if (V::not_null($data['peopleNo'])) {
            $entity->setPeopleNo($data['peopleNo']);
        }
        if (V::not_null($data['cookingTime'])) {
            $entity->setCookingTime($data['cookingTime']);
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
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم التعديل بنجاح');
        return $this->redirect($this->generateUrl('fe_my_recipe'));
    }

    /**
     * Set Images to Property.
     *
     * @Route("/gallery-image-recipe/{id}/" , name="fe_recipe_edit_create_images")
     * @Method("POST")
     */
    public function setRecipeImageAction(Request $request, $id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $form = $this->createForm(new \MD\Bundle\MediaBundle\Form\ImageType());
        $formView = $form->createView();
        $form->bind($request);

        $data = $form->getData();
        $files = $data["files"];

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('CMSBundle:Recipe')->find($id);
        $entity = $recipe->getPost();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Landmark entity.');
        }

        $uploadPath = 'recipe/';

        $imageType = $request->get("type");
        foreach ($files as $file) {
            if ($file != NULL) {
                $image = new \MD\Bundle\MediaBundle\Entity\Image();
                $em->persist($image);
                $em->flush();
                $image->setFile($file);
                $mainImages = $entity->getImages(array(\MD\Bundle\MediaBundle\Entity\Image::TYPE_MAIN));
                if ($imageType == Image::TYPE_MAIN && count($mainImages) > 0) {
                    foreach ($mainImages As $mainImage) {
                        $entity->removeImage($mainImage);
                        $em->persist($entity);
                        $em->flush();
                        $image->storeFilenameForRemove($uploadPath . 'image/' . $entity->getId());
                        $image->removeUpload();
//                        $image->storeFilenameForResizeRemove("suppliers/" . $entity->getId());
//                        $image->removeResizeUpload();
                        $em->persist($mainImage);
                        $em->flush();
                        $em->remove($mainImage);
                        $em->flush();
                        $image->setImageType(Image::TYPE_MAIN);
                    }
                } else if (count($mainImages) == 0) {
                    $image->setImageType(Image::TYPE_MAIN);
                } else {
                    $image->setImageType(Image::TYPE_GALLERY);
                }


                $image->preUpload();
                $image->upload($uploadPath . 'image/' . $entity->getId());
                $entity->addImage($image);
                $imageUrl = $this->container->get('templating.helper.assets')->getUrl("uploads/" . $uploadPath . 'image/' . $entity->getId() . "/" . $image->getId());
                $imageId = $image->getId();
            }
            $em->persist($entity);
            $em->flush();
            $files = '{"files":[{"url":"' . $imageUrl . '","thumbnailUrl":"http://lh6.ggpht.com/0GmazPJ8DqFO09TGp-OVK_LUKtQh0BQnTFXNdqN-5bCeVSULfEkCAifm6p9V_FXyYHgmQvkJoeONZmuxkTBqZANbc94xp-Av=s80","name":"test","id":"' . $imageId . '","type":"image/jpeg","size":620888,"deleteUrl":"http://localhost/packagat/web/uploads/packages/1/th71?delete=true","deleteType":"DELETE"}]}';
            $response = new Response();
            $response->setContent($files);
            $response->setStatusCode(200);
            return $response;
        }

        return array(
            'form' => $formView,
            'id' => $id,
        );
    }

    /**
     * Displays a form to create a new PropertyGallery entity.
     *
     * @Route("/gallery/ajax/", name = "fe_recipe_edit_main_image_ajax")
     * @Method("POST")
     */
    public function SetimageMainAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $image_id = $this->getRequest()->request->get('image_id');
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('MediaBundle:Image')->setMainImage('CMSBundle:Post', $id, $image_id);
    }

    /**
     * Deletes a PropertyGallery entity.
     *
     * @Route("/delete-recipe-image/{id}", name="fe_recipe_edit_images_delete")
     * @Method("POST")
     */
    public function deleteRecipeImageAction($id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $image_id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('CMSBundle:Recipe')->find($id);
        $entity = $em->getRepository('CMSBundle:Post')->find($recipe->getPost()->getId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to Post Team entity.');
        }
        $image = $em->getRepository('MediaBundle:Image')->find($image_id);
        if (!$image) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }
        $entity->removeImage($image);
        $em->persist($entity);
        $em->flush();

        $uploadPath = 'recipe/';

        $image->storeFilenameForRemove($uploadPath . 'image/' . $entity->getId());
        $image->removeUpload();
//        $image->storeFilenameForResizeRemove($uploadPath . $h_id);
//        $image->removeResizeUpload();
//        $em->persist($image);
//        $em->flush();
        $em->remove($image);
        $em->flush();

        return $this->redirect($this->generateUrl('fe_recipe_edit', array('slug' => $recipe->getSeo()->getSlug())));
    }

}
