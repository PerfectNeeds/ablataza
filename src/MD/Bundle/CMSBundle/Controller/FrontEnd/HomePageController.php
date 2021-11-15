<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Utils\Validate;

/**
 * HomePage controller.
 *
 * @Route("")
 */
class HomePageController extends Controller {

    /**
     * Lists all Home entities.
     *
     * @Route("/ajax-testt", name="fe_ajax_test")
     * @Method("POST")
     * @Template()
     */
    public function ajaxTestAction() {
        $id = $this->getRequest()->request->get('id');
        return new \Symfony\Component\HttpFoundation\Response('Done ' . $id);
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/ajax-test", name="fe_ajax_test_page")
     * @Method("GET")
     * @Template()
     */
    public function ajaxTestPageAction() {
        return array();
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/", name="fe_home")
     * @Method("GET")
     * @Template()
     */
    public function homeAction() {
        $em = $this->getDoctrine()->getManager();
        $homepage = $em->getRepository('CMSBundle:DynamicPage')->find(1);

        $chefOfTheMonthEntity = $em->getRepository('CMSBundle:SiteSetting')->find(1);
        if (\MD\Utils\Validate::not_null($chefOfTheMonthEntity->getValue())) {
            $chefOfTheMonth = $em->getRepository('UserBundle:Person')->find($chefOfTheMonthEntity->getValue());
            if (!$chefOfTheMonth) {
                $chefOfTheMonth = NULL;
            }
        } else {
            $chefOfTheMonth = NULL;
        }

        $ablatazzaRecipes = $em->getRepository('CMSBundle:Recipe')->getLatestRecipeByUserIdAndLimit(1, 2);
        $userRecipes = $em->getRepository('CMSBundle:Recipe')->getLatestRecipeByNotEqualUserIdAndLimit(1, 3);
        $mostViewRecipe = $em->getRepository('CMSBundle:Recipe')->getMostViewRecipeByUserIdAndLimit(1, 1);
        $mostViewArticle = $em->getRepository('CMSBundle:Article')->getMostViewArticleByUserIdAndLimit(1, 1);
        $fadfadas = $em->getRepository('CMSBundle:Fadfada')->findBy(array('publish' => TRUE), array('id' => 'DESC'), 2);
        $asks = $em->getRepository('CMSBundle:Ask')->findBy(array('publish' => TRUE), array('id' => 'DESC'), 5);
        $ablatazzaTip = $em->getRepository('CMSBundle:Tip')->findOneRandByUserId(1);
        $usersTip = $em->getRepository('CMSBundle:Tip')->findOneRandByNotEqualUserId(1);
        $ablatazzaArticles = $em->getRepository('CMSBundle:Article')->getLatestArticleByUserIdAndLimit(1, 2);
        $userArticles = $em->getRepository('CMSBundle:Article')->getLatestArticleByNotEqualUserIdAndLimit(1, 2);
        $survey = $em->getRepository('CMSBundle:Survey')->findOneRand();
        $result = array();
        if ($this->getUser() AND $survey != FALSE AND $survey->isePersonAnswer($this->getUser()->getPerson()->getId()) == true) {
            $surveyRate = $em->getRepository('CMSBundle:Survey')->getSurveyRate($survey->getId());

            $totalRate = 0;
            foreach ($surveyRate as $key => $value) {
                $totalRate +=$value;
            }


            if (Validate::not_null($survey->getAnswer1())) {
                if (isset($surveyRate['1'])) {
                    $rate = ( $surveyRate['1'] / $totalRate ) * 100;
                    $result[] = array('answer' => $survey->getAnswer1(), 'rate' => number_format($rate));
                } else {
                    $result[] = array('answer' => $survey->getAnswer1(), 'rate' => 0);
                }
            }
            if (Validate::not_null($survey->getAnswer2())) {
                if (isset($surveyRate['2'])) {
                    $rate = ($surveyRate['2'] / $totalRate) * 100;
                    $result[] = array('answer' => $survey->getAnswer2(), 'rate' => number_format($rate));
                } else {
                    $result[] = array('answer' => $survey->getAnswer2(), 'rate' => 0);
                }
            }
            if (Validate::not_null($survey->getAnswer3())) {
                if (isset($surveyRate['3'])) {
                    $rate = ($surveyRate['3'] / $totalRate ) * 100;
                    $result[] = array('answer' => $survey->getAnswer3(), 'rate' => number_format($rate));
                } else {
                    $result[] = array('answer' => $survey->getAnswer3(), 'rate' => 0);
                }
            }
            if (Validate::not_null($survey->getAnswer4())) {
                if (isset($surveyRate['4'])) {
                    $rate = ($surveyRate['4'] / $totalRate ) * 100;
                    $result[] = array('answer' => $survey->getAnswer4(), 'rate' => number_format($rate));
                } else {
                    $result[] = array('answer' => $survey->getAnswer4(), 'rate' => 0);
                }
            }
        }
        $this->addSeoForPerson();

        return array(
            'homepage' => $homepage,
            'ablatazzaRecipes' => $ablatazzaRecipes,
            'userRecipes' => $userRecipes,
            'mostViewRecipe' => $mostViewRecipe,
            'mostViewArticle' => $mostViewArticle,
            'fadfadas' => $fadfadas,
            'asks' => $asks,
            'ablatazzaTip' => $ablatazzaTip,
            'usersTip' => $usersTip,
            'ablatazzaArticles' => $ablatazzaArticles,
            'userArticles' => $userArticles,
            'chefOfTheMonth' => $chefOfTheMonth,
            'survey' => $survey,
            'surveyResult' => $result,
        );
    }

    private function addSeoForPerson() {
        $em = $this->getDoctrine()->getManager();
        $person = new \MD\Bundle\UserBundle\Entity\Person;
        $persons = $em->getRepository('UserBundle:Person')->findAll();
        foreach ($persons as $person) {
            if ($person->getSeo() == NULL) {
                $seo = new \MD\Bundle\CMSBundle\Entity\Seo;
                $seo->setTitle($person->getFirstName() . ' ' . $person->getFamilyname());
                $seo->setSlug('person/' . $person->getFirstName() . '-' . $person->getFamilyname() . '-' . $person->getId());
                $person->setSeo($seo);
                $em->persist($person);
                $em->flush();
            }
        }
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/chef-of-month", name="fe_chef_month")
     * @Method("GET")
     * @Template("")
     */
    public function chefOfTheMonthAction() {
        $em = $this->getDoctrine()->getManager();
        $chefOfTheMonthEntity = $em->getRepository('CMSBundle:SiteSetting')->find(1);
        $chefOfTheMonthRecipeCount = 0;

        $chefOfTheMonth = NULL;
        $chefOfTheMonthId = NULL;

        if (\MD\Utils\Validate::not_null($chefOfTheMonthEntity->getValue())) {
            $chefOfTheMonth = $em->getRepository('UserBundle:Person')->find($chefOfTheMonthEntity->getValue());
            if ($chefOfTheMonth) {
                $search = new \stdClass;
                $search->draft = TRUE;
                $search->publish = TRUE;
                $search->userId = $chefOfTheMonth->getId();
                $count = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
                $chefOfTheMonthRecipeCount = $count;
                $chefOfTheMonthId = $chefOfTheMonth->getId();
            }
        }

        $topPersons = $em->getRepository('CMSBundle:Recipe')->getTopUserByRecipeCount(20, $chefOfTheMonthId);

        return array(
            'chefOfTheMonth' => $chefOfTheMonth,
            'chefOfTheMonthRecipeCount' => $chefOfTheMonthRecipeCount,
            'topPersons' => $topPersons,
        );
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/search", name="fe_search")
     * @Method("GET")
     * @Template("CMSBundle:FrontEnd\HomePage:searchResult.html.twig")
     */
    public function searchAction() {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $search->string = $this->getRequest()->get('s');

        $recipeCount = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $recipepPaginator = new \MD\Bundle\CMSBundle\Lib\Paginator($recipeCount, 1, 6);
        $recipes = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $recipepPaginator->getLimitStart(), $recipepPaginator->getPageLimit());

        $articleCount = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $articlePaginator = new \MD\Bundle\CMSBundle\Lib\Paginator($articleCount, 1, 6);
        $articles = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $articlePaginator->getLimitStart(), $articlePaginator->getPageLimit());

        $tipCount = $em->getRepository('CMSBundle:Tip')->filter($search, TRUE);
        $tipPaginator = new \MD\Bundle\CMSBundle\Lib\Paginator($tipCount, 1, 4);
        $tips = $em->getRepository('CMSBundle:Tip')->filter($search, FALSE, $tipPaginator->getLimitStart(), $tipPaginator->getPageLimit());
        return array(
            'recipes' => $recipes,
            'recipeCount' => $recipeCount,
            'articles' => $articles,
            'articleCount' => $articleCount,
            'ablaTazzaTips' => $tips,
            'tipCount' => $tipCount,
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/search/recipe/{page}", requirements={"page" = "\d+"}, name="fe_search_recipe")
     * @Method("GET")
     * @Template()
     */
    public function searchRecipeAction($page = 1) {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $search->string = $this->getRequest()->get('s');

        $count = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 9);
        $entities = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());


        return array(
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/search/article/{page}", requirements={"page" = "\d+"}, name="fe_search_article")
     * @Method("GET")
     * @Template()
     */
    public function searchArticleAction($page = 1) {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $search->string = $this->getRequest()->get('s');

        $count = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 10);
        $entities = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());


        return array(
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/search/tip/{page}", requirements={"page" = "\d+"}, name="fe_search_tip")
     * @Method("GET")
     * @Template()
     */
    public function searchTipAction($page = 1) {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $search->string = $this->getRequest()->get('s');

        $count = $em->getRepository('CMSBundle:Tip')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 10);
        $entities = $em->getRepository('CMSBundle:Tip')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());


        return array(
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/privacy-policy", name="fe_privacy")
     * @Method("GET")
     * @Template("CMSBundle:FrontEnd\HomePage:privacyPolicy.html.twig")
     */
    public function privacyPolicyAction() {
        $em = $this->getDoctrine()->getManager();
        $privacy = $em->getRepository('CMSBundle:DynamicPage')->find(5);

        return array(
            'privacy' => $privacy,
        );
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/dinners-invite", name="fe_abla_tazza_menu_planner")
     * @Method("GET")
     * @Template("CMSBundle:FrontEnd\HomePage:ablaTazzaMenuPlanner.html.twig")
     */
    public function ablaTazzaMenuPlannerAction() {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('CMSBundle:DynamicPage')->find(6);

        $tt = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findByMenuPlanner(43);
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => 1));
        foreach ($menuPlanners as $menuPlanner) {
            $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
            if ($menuPlannerFirstRecipe) {
                $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
            } else {
                $menuPlanner->recipe = NULL;
            }
        }

        return array(
            'page' => $page,
            'menuPlanners' => $menuPlanners,
            'tt' => $tt,
        );
    }

    /**
     * Lists all Home entities.
     *
     * @Route("/quotes/{page}", requirements={"page" = "\d+"}, name="fe_abla_tazza_quote")
     * @Method("GET")
     * @Template("CMSBundle:FrontEnd\HomePage:quote.html.twig")
     */
    public function quoteAction($page = 1) {
        $em = $this->getDoctrine()->getManager();
        $search = new \stdClass();
        $search->publish = TRUE;

        $person = $em->getRepository('UserBundle:Person')->find(1);

        $count = $em->getRepository('CMSBundle:Quote')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 8);
        $entities = $em->getRepository('CMSBundle:Quote')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $page = $em->getRepository('CMSBundle:DynamicPage')->find(6);
        return array(
            'page' => $page,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'person' => $person,
        );
    }

    /**
     * @Template("CMSBundle:FrontEnd\HomePage:menu.html.twig")
     */
    public function menuAction() {

        $em = $this->getDoctrine()->getManager();
        $superCategoris = $em->getRepository('CMSBundle:SuperCategory')->findAll();
        return array(
            'superCategoris' => $superCategoris,
        );
    }

    /**
     * @Template("CMSBundle:FrontEnd\HomePage:sitemap.html.twig")
     */
    public function sitemapAction() {

        $em = $this->getDoctrine()->getManager();
        $superCategoris = $em->getRepository('CMSBundle:SuperCategory')->findAll();
        return array(
            'superCategoris' => $superCategoris,
        );
    }

    /**
     * @Template("CMSBundle:FrontEnd\HomePage:notificationWidget.html.twig")
     */
    public function notificationWidgetAction() {

        $em = $this->getDoctrine()->getManager();
        $personId = $this->getUser()->getPerson()->getId();

        $notifications = $em->getRepository('UserBundle:Follower')->getFollowerNotification($personId, 2);
        return array(
            'notifications' => $notifications,
        );
    }

}
