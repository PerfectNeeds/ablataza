<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MD\Bundle\CMSBundle\Entity\RecipeFavorite;
use MD\Utils\Validate;

/**
 * Recipe controller.
 *
 * @Route("/recipe")
 */
class RecipeController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/{slug}/{page}", requirements={"page" = "\d+"}, name="fe_recipe")
     * @Method("GET")
     * @Template()
     */
    public function recipeAction($slug = null, $page = 1) {
        $em = $this->getDoctrine()->getManager();
        $pageSeo = $em->getRepository('CMSBundle:DynamicPage')->find(2);

        $search = new \stdClass();
        $search->category = '';
        $search->subCategory = '';
        if ($slug != null) {
            $superCategory = $em->getRepository('CMSBundle:SuperCategory')->findOneBySlug($slug);
            if ($superCategory == null) {
                $category = $em->getRepository('CMSBundle:Category')->findOneBySlug($slug);
                if ($category == null) {
                    $subCategory = $em->getRepository('CMSBundle:SubCategory')->findOneBySlug($slug);
                    if ($subCategory != null) {
                        $search->subCategory = $subCategory->getId();
                        $seo = $subCategory->getSeo();
                    } else {
                        throw new NotFoundHttpException();
                    }
                } else {
                    $search->category = $category->getId();
                    $seo = $category->getSeo();
                }
            } else {
                $seo = $superCategory->getSeo();
            }
        } else {
            $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find(1);
            $seo = $superCategory->getSeo();
        }

        $categories = $em->getRepository('CMSBundle:Category')->findBy(array('superCategory' => 1));
//        $collectPost = $this->collectSearch();
        $count = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page);
        $entities = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $ablaTazzaTip = $em->getRepository('CMSBundle:Tip')->findOneRandByUserIdAndSuperCategoryId(1, 1);
        $userTip = $em->getRepository('CMSBundle:Tip')->findOneRandByNotEqualUserIdAndSuperCategoryId(1, 1);
        $subCategory = NULL;
        $category = NULL;
        $session = $this->getRequest()->getSession();
        if (Validate::not_null($search->category)) {
            $category = $em->getRepository('CMSBundle:Category')->find($search->category);
        }
        if (Validate::not_null($search->subCategory)) {
            $subCategory = $em->getRepository('CMSBundle:SubCategory')->find($search->subCategory);
            $session->set('recipeTab', $subCategory);
        } else {
            $session->remove('recipeTab');
        }

        return array(
            'page' => $pageSeo,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'ablaTazzaTip' => $ablaTazzaTip,
            'userTip' => $userTip,
            'subCategory' => $subCategory,
            'category' => $category,
            'seo' => $seo,
            'categories' => $categories,
        );
    }

    public function collectSearch() {
        $search = new \stdClass();
        $search->category = $this->getRequest()->query->get('c');
        $search->subCategory = $this->getRequest()->query->get('sc');
        return $search;
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/show/{slug}", name="fe_show_recipe")
     * @Method("GET")
     * @Template()
     */
    public function showRecipeAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(2);
        $entity = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $comments = $em->getRepository('CMSBundle:RecipeComment')->findBy(array('recipe' => $entity->getId(), 'publish' => TRUE));

        $session = $this->getRequest()->getSession();
        if (!$session->get('RecipeViews') || !in_array($entity->getId(), $session->get('RecipeViews'))) {
            $temp = (array) $session->get('RecipeViews');
            array_push($temp, $entity->getId());
            $session->set('RecipeViews', $temp);
            $noViews = ($entity->getViews() == null) ? 0 : $entity->getViews();
            $noViews++;
            $entity->setViews($noViews);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }

        $categoryArray = array();
        foreach ($entity->getCategories() as $subCategory) {
            $categoryArray[] = $subCategory->getId();
        }
        if ($session->has('recipeTab')) {
            if (in_array($session->get('recipeTab')->getId(), $categoryArray)) {
                $currentTab = $session->get('articleTab');
            } else {
                if (count($entity->getCategories()) > 0) {
                    $currentTab = $entity->getCategories()[0];
                } else {
                    $currentTab = $entity->getSubCategories()[0]->getCategory();
                }
            }
        } else {
            if (count($entity->getCategories()) > 0) {
                $currentTab = $entity->getCategories()[0];
            } else {
                $currentTab = $em->getRepository('CMSBundle:Category')->find(1);
            }
        }
        if (count($entity->getSubCategories()) > 0) {
            $similars = $em->getRepository('CMSBundle:Recipe')->getSimilar($entity->getId(), $entity->getSubCategories()[0]->getId());
        } else {
            $similars = $em->getRepository('CMSBundle:Recipe')->getSimilar($entity->getId(), 0);
        }

        $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find(1);
        if ($this->getUser() != NULL) {
            $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $this->getUser()->getPerson()->getId()));
        } else {
            $menuPlanners = NULL;
        }

        $backMenuPlanner = NULL;
        if ($session->has('menuPlannerHistory')) {
            $menuPlannerHistory = $session->get('menuPlannerHistory');
            $backMenuPlanner = new \stdClass;
            $backMenuPlanner->routeName = $menuPlannerHistory->routeName;
            $backMenuPlanner->entity = $em->getRepository('UserBundle:MenuPlanner')->find($menuPlannerHistory->id);
        }

        return array(
            'page' => $page,
            'entity' => $entity,
            'comments' => $comments,
            'similars' => $similars,
            'currentTab' => $currentTab,
            'superCategory' => $superCategory,
            'menuPlanners' => $menuPlanners,
            'backMenuPlanner' => $backMenuPlanner,
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/favorite/ajax", name="fe_recipe_favorite_ajax")
     * @Method("POST")
     */
    public function favoriteAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $checkFavorite = $em->getRepository('CMSBundle:RecipeFavorite')->findOneBy(array('recipe' => $id, 'person' => $this->getUser()->getId()));
        if (!$checkFavorite) {
            $recipeFavorite = new RecipeFavorite();
            $recipeFavorite->setPerson($this->getUser()->getPerson());
            $recipe = $em->getRepository('CMSBundle:Recipe')->find($id);
            $recipeFavorite->setRecipe($recipe);
            $em->persist($recipeFavorite);
            $em->flush();

            $value = 1;
        } else {
            $em->remove($checkFavorite);
            $em->flush();
            $value = 0;
        }
        return new \Symfony\Component\HttpFoundation\Response($value);
    }

    protected function collectCommentPOST() {
        $comment = new \stdClass();
        $comment->name = $this->getRequest()->get('name');
        $comment->comment = $this->getRequest()->get('comment');
        $comment->rating = $this->getRequest()->get('rating');
        return $comment;
    }

    public function validateComment($comment) {
        $return = TRUE;
        $errors = array();

        if (!Validate::not_null($comment->name)) {
            $message = $this->get('translator')->trans("You must enter your name");
            array_push($errors, $message);
        }

        if (!Validate::not_null($comment->comment)) {
            $message = "You must enter you comment";
            array_push($errors, $message);
        }
        if (Validate::not_null($comment->rating) AND ! is_numeric($comment->rating) AND $comment->rating == 0) {
            $message = "You must enter a valid rating";
            array_push($errors, $message);
        }


        if (count($errors) > 0) {
            $return = '';
            for ($i = 0; $i < count($errors); $i++) {
                if (count($errors) == $i + 1) {
                    $return .= $errors[$i];
                } else {
                    $return .= $errors[$i] . ' and ';
                }
            }
        }
        return $return;
    }

    /**
     * Creates a new BloggerComment entity.
     *
     * @Route("/{slug}", name="fe_recipecomment_create")
     * @Method("POST")
     * @Template("CMSBundle:FrontEnd/showRecipe:new.html.twig")
     */
    public function createCommentAction($slug) {
        $entity = new \MD\Bundle\CMSBundle\Entity\RecipeComment();

        $collectPost = $this->collectCommentPOST();
        $validate = $this->validateComment($collectPost);
        if ($validate !== TRUE) {
            $session = new \Symfony\Component\HttpFoundation\Session\Session();
            $session->getFlashBag()->add('commentError', $validate);
            return $this->redirect($this->generateUrl('fe_recipe', array('slug' => $slug)) . '#comment');
        }

        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);
        $entity->setRecipe($recipe);
        $entity->setName($collectPost->name);
        $entity->setComment($collectPost->comment);
        if (Validate::not_null($collectPost->rating)) {
            $entity->setRating($collectPost->rating);
        } else {
            $entity->setRating(NULL);
        }

        $em->persist($entity);
        $em->flush();

        $session = new \Symfony\Component\HttpFoundation\Session\Session();
        $session->getFlashBag()->add('commentSuccessRecipe', $validate);

        $message = array(
            'subject' => 'Abla Tazza Recipe Comment',
            'from' => 'info@ablatazza.com',
            'to' => array('nourhan.shweter@4saleegypt.com ', 'jailan.elbayaa@4saleegypt.com', 'hams.alaa@4saleegypt.com', 'ablatazza@gmail.com'),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Recipe:commentEmail.html.twig', array(
                'parm' => $collectPost,
                'entity' => $entity,
                'recipe' => $recipe
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        return $this->redirect($this->generateUrl('fe_show_recipe', array('slug' => $slug)));
    }

}
