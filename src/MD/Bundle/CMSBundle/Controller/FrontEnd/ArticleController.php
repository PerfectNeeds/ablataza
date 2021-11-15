<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MD\Bundle\CMSBundle\Entity\ArticleFavorite;
use MD\Utils\Validate;

/**
 * Article controller.
 *
 * @Route("/article")
 */
class ArticleController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/{slug}/{page}", requirements={"page" = ".+", "page" = "\d+"}, name="fe_article")
     * @Method("GET")
     * @Template()
     */
    public function articleAction($slug = null, $page = 1) {
        $em = $this->getDoctrine()->getManager();
        $pageSeo = $em->getRepository('CMSBundle:DynamicPage')->find(4);

//        $collectPost = $this->collectSearch();

        $search = new \stdClass();
        $search->category = '';
        $search->superCategory = '';
        if ($slug != null) {
            $superCategory = $em->getRepository('CMSBundle:SuperCategory')->findOneBySlug($slug);
            if ($superCategory == null) {
                $category = $em->getRepository('CMSBundle:Category')->findOneBySlug($slug);
                if ($category != null) {
                    $search->category = $category->getId();
                    $seo = $category->getSeo();
                } else {
                    throw new NotFoundHttpException();
                }
            } else {
                $search->superCategory = $superCategory->getId();
                $seo = $superCategory->getSeo();
            }
        }

        $count = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 10);
        $entities = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $ablaTazzaTip = $em->getRepository('CMSBundle:Tip')->findOneRandByUserId(1);
        $userTip = $em->getRepository('CMSBundle:Tip')->findOneRandByNotEqualUserId(1);
        $superCategory = NULL;
        $category = NULL;
        $session = $this->getRequest()->getSession();
        if (Validate::not_null($search->category)) {
            $category = $em->getRepository('CMSBundle:Category')->find($search->category);
            $session->set('articleTab', $category->getSuperCategory());
        }
        if (Validate::not_null($search->superCategory)) {
            $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($search->superCategory);
            $session->set('articleTab', $superCategory);

            $ablaTazzaTip = $em->getRepository('CMSBundle:Tip')->findOneRandByUserIdAndSuperCategoryId(1, $search->superCategory);
            $userTip = $em->getRepository('CMSBundle:Tip')->findOneRandByNotEqualUserIdAndSuperCategoryId(1, $search->superCategory);
        }
        if (!Validate::not_null($search->category) AND ! Validate::not_null($search->superCategory)) {
            $session->remove('articleTab');
        }

        $currentTab = $session->get('articleTab');
        return array(
            'page' => $pageSeo,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'ablaTazzaTip' => $ablaTazzaTip,
            'userTip' => $userTip,
            'superCategory' => $superCategory,
            'category' => $category,
            'currentTab' => $currentTab,
            'seo' => $seo,
        );
    }

    public function collectSearch() {
        $search = new \stdClass();
        $search->category = $this->getRequest()->query->get('c');
        $search->superCategory = $this->getRequest()->query->get('sc');
        return $search;
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/show/{slug}", name="fe_show_article")
     * @Method("GET")
     * @Template()
     */
    public function showArticleAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(4);
        $entity = $em->getRepository('CMSBundle:Article')->findOneBySlug($slug);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $comments = $em->getRepository('CMSBundle:ArticleComment')->findBy(array('article' => $entity->getId(), 'publish' => TRUE));

        $session = $this->getRequest()->getSession();
        if (!$session->get('ArticleViews') || !in_array($entity->getId(), $session->get('ArticleViews'))) {
            $temp = (array) $session->get('ArticleViews');
            array_push($temp, $entity->getId());
            $session->set('ArticleViews', $temp);
            $noViews = ($entity->getViews() == null) ? 0 : $entity->getViews();
            $noViews++;
            $entity->setViews($noViews);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }

        $superCategoryArray = array();
        foreach ($entity->getSuperCategories() as $superCategory) {
            $superCategoryArray[] = $superCategory->getId();
        }
        if ($session->has('articleTab')) {
            if ($session->has('articleTab') AND in_array($session->get('articleTab')->getId(), $superCategoryArray)) {
                $currentTab = $session->get('articleTab');
            } else {
                $currentTab = $entity->getSuperCategories()[0];
            }
        } else {
            if (count($entity->getSuperCategories()) > 0) {
                $currentTab = $entity->getSuperCategories()[0];
            } else {
                $currentTab = $em->getRepository('CMSBundle:SuperCategory')->find(2);
            }
        }
        $similars = $em->getRepository('CMSBundle:Article')->getRelatedArticleLimit($entity, 3);

        return array(
            'page' => $page,
            'entity' => $entity,
            'comments' => $comments,
            'similars' => $similars,
            'currentTab' => $currentTab,
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/favorite/ajax", name="fe_article_favorite_ajax")
     * @Method("POST")
     */
    public function favoriteAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $checkFavorite = $em->getRepository('CMSBundle:ArticleFavorite')->findOneBy(array('article' => $id, 'person' => $this->getUser()->getId()));
        if (!$checkFavorite) {
            $articleFavorite = new ArticleFavorite();
            $articleFavorite->setPerson($this->getUser()->getPerson());
            $article = $em->getRepository('CMSBundle:Article')->find($id);
            $articleFavorite->setArticle($article);
            $em->persist($articleFavorite);
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
     * @Route("/{slug}", name="fe_articlecomment_create")
     * @Method("POST")
     * @Template("CMSBundle:FrontEnd/showArticle:new.html.twig")
     */
    public function createCommentAction($slug) {
        $entity = new \MD\Bundle\CMSBundle\Entity\ArticleComment();

        $collectPost = $this->collectCommentPOST();
        $validate = $this->validateComment($collectPost);
        if ($validate !== TRUE) {
            $session = new \Symfony\Component\HttpFoundation\Session\Session();
            $session->getFlashBag()->add('commentError', $validate);
            return $this->redirect($this->generateUrl('fe_article', array('slug' => $slug)) . '#comment');
        }

        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository('CMSBundle:Article')->findOneBySlug($slug);
        $entity->setArticle($article);
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
        $session->getFlashBag()->add('commentSuccessArticle', $validate);

        $message = array(
            'subject' => 'Abla Tazza Article Comment',
            'from' => 'info@ablatazza.com',
            'to' => array('nourhan.shweter@4saleegypt.com ', 'jailan.elbayaa@4saleegypt.com', 'hams.alaa@4saleegypt.com', 'ablatazza@gmail.com'),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Article:commentEmail.html.twig', array(
                'parm' => $collectPost,
                'entity' => $entity,
                'article' => $article
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);


        return $this->redirect($this->generateUrl('fe_show_article', array('slug' => $slug)));
    }

}
