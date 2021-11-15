<?php

namespace MD\Bundle\UserBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MD\Utils\Validate as V;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\UserBundle\Entity\MenuPlanner;
use MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * MenuPlanner controller.
 * @Route("/menu-planner")
 */
class MenuPlannerController extends Controller {

    function __construct() {
        $session = new Session();
        if ($session->has('menuPlannerHistory')) {
            $session->remove('menuPlannerHistory');
        }
    }

    /**
     * Create a new Account entity.
     * @Route("/{page}/{sort}", requirements={"page" = "\d+", "sort" = "\d+"}, name="fe_menu_planner")
     * @Method("GET")
     * @Template()
     */
    public function menuPlannerAction($page = 1, $sort = 1) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser()->getPerson();
        $search = new \stdClass();
        $search->userId = $user->getId();

        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $user->getId()), array('id' => 'DESC'));
        $count = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 8);
        $entities = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, FALSE, $sort, $paginator->getLimitStart(), $paginator->getPageLimit());

        $category = $em->getRepository('CMSBundle:SuperCategory')->find(1);

        return array(
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'menuPlanners' => $menuPlanners,
            'category' => $category,
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/show/{slug}/{page}/{sort}", requirements={"page" = "\d+", "sort" = "\d+"}, name="fe_menu_planner_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($slug, $page = 1, $sort = 1) {
        $em = $this->getDoctrine()->getManager();
        $menuPlanner = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);
        $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
        if ($menuPlannerFirstRecipe) {
            $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
        } else {
            $menuPlanner->recipe = NULL;
        }

        if (!$menuPlanner) {
            throw new NotFoundHttpException();
        }

        $session = new Session;

        $menuPlannerHistory = new \stdClass;
        $menuPlannerHistory->routeName = 'fe_menu_planner_show';
        $menuPlannerHistory->id = $menuPlanner->getId();
        $session->set('menuPlannerHistory', $menuPlannerHistory);

        $search = new \stdClass();
        $search->menuPlannerId = $menuPlanner->getId();

        $count = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 8);
        $entities = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, FALSE, $sort, $paginator->getLimitStart(), $paginator->getPageLimit());
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $menuPlanner->getPerson()->getId()), array('id' => 'DESC'));

        $category = $em->getRepository('CMSBundle:SuperCategory')->find(1);

        $permission = FALSE;
        if ($this->getUser() AND $this->getUser()->getPerson()->getId() == $menuPlanner->getPerson()->getId()) {
            $permission = TRUE;
        }
        return array(
            'menuPlanner' => $menuPlanner,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'menuPlanners' => $menuPlanners,
            'permission' => $permission,
            'category' => $category,
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/show-abla-tazza/{slug}/{page}", requirements={"page" = "\d+"}, name="fe_menu_planner_show_abla_tazza")
     * @Method("GET")
     * @Template()
     */
    public function showAblaTazzaAction($slug, $page = 1) {
        $em = $this->getDoctrine()->getManager();
        $menuPlanner = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);
        $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
        if ($menuPlannerFirstRecipe) {
            $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
        } else {
            $menuPlanner->recipe = NULL;
        }

        if (!$menuPlanner) {
            throw new NotFoundHttpException();
        }
        $session = new Session;

        $menuPlannerHistory = new \stdClass;
        $menuPlannerHistory->routeName = 'fe_menu_planner_show_abla_tazza';
        $menuPlannerHistory->id = $menuPlanner->getId();
        $session->set('menuPlannerHistory', $menuPlannerHistory);

        $search = new \stdClass();
        $search->menuPlannerId = $menuPlanner->getId();

        $count = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 9);
        $entities = $em->getRepository('UserBundle:MenuPlanner')->filterRecipe($search, FALSE, 1, $paginator->getLimitStart(), $paginator->getPageLimit());
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $menuPlanner->getPerson()->getId()), array('id' => 'DESC'));


        return array(
            'menuPlanner' => $menuPlanner,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'menuPlanners' => $menuPlanners,
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/manage", name="fe_menu_planner_manage")
     * @Method("GET")
     * @Template()
     */
    public function manageAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->getDoctrine()->getManager();
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $this->getUser()->getPerson()->getId()), array('id' => 'DESC'));
        foreach ($menuPlanners as $menuPlanner) {
            $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
            if ($menuPlannerFirstRecipe) {
                $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
            } else {
                $menuPlanner->recipe = NULL;
            }
        }
        $category = $em->getRepository('CMSBundle:SuperCategory')->find(1);

        return array(
            'menuPlanners' => $menuPlanners,
            'category' => $category,
        );
    }

    private function collectPOST() {
        $menuPlanner = new \stdClass;
        $menuPlanner->title = $this->getRequest()->request->get('title');
        $menuPlanner->description = $this->getRequest()->request->get('description');
        $ajax = $this->getRequest()->request->get('ajax');
        $menuPlanner->ajax = (isset($ajax)) ? TRUE : FALSE;
        return $menuPlanner;
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/create-menu-planner/{slug}", name="fe_menu_planner_create")
     * @Method("POST")
     * @Template("")
     */
    public function createMenuPlannerAction($slug = null) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $entity = new MenuPlanner();

        $em = $this->getDoctrine()->getManager();

        $collectPOST = $this->collectPOST();
        $return = TRUE;
        $error = array();
        if (!V::not_null($collectPOST->title)) {
            array_push($error, "يرجى إدخال عنوان مجلد");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = $error[0];
            if ($collectPOST->ajax == TRUE) {
                return new \Symfony\Component\HttpFoundation\Response(json_encode(array('error' => 1, 'message' => $return)));
            }

            $session = new Session();
            $session->getFlashBag()->add('error', $return);

            return $this->redirect($this->generateUrl('fe_menu_planner'));
        }

        $entity->setTitle($collectPOST->title);
        $entity->setDescription($collectPOST->description);



        $person = $em->getRepository('UserBundle:Person')->find($this->getUser()->getPerson()->getId());
        $entity->setPerson($person);
        $em->persist($entity);
        $em->flush();

        $seoEntity = new Seo();
        $seoEntity->setTitle($entity->getTitle());
        $seoEntity->setSlug('menu-planner/' . $entity->getId() . '-' . $entity->getTitle());
        $em->persist($seoEntity);
        $em->flush();

        $entity->setSeo($seoEntity);
        $em->persist($entity);
        $em->flush();

        if ($slug != NULL) {
            $recipe = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);
            $menuPlannerHasRecipe = new MenuPlannerHasRecipe();
            $menuPlannerHasRecipe->setMenuPlanner($entity);
            $menuPlannerHasRecipe->setRecipe($recipe);
            $em->persist($menuPlannerHasRecipe);
            $em->flush();
        }


        if ($collectPOST->ajax == TRUE) {
            $return = array(
                'error' => 0,
                'message' => $this->renderView('UserBundle:FrontEnd/MenuPlanner:menuPlannerItemAjax.html.twig', array(
                    'menuPlanner' => $entity
                ))
            );

            return new Response(json_encode($return));
        }

        $session = new Session();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم إضافة المجلد بنجاح');
        return $this->redirect($this->generateUrl('fe_menu_planner_show', array('slug' => $entity->getSeo()->getSlug())));
    }

    /**
     * Edits an existing MenuPlanner entity.
     *
     * @Route("/update/{slug}", name="fe_menu_planner_update")
     * @Method("POST")
     * @Template()
     */
    public function updateMenuPlannerAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MenuPlanner entity.');
        }

        $collectPOST = $this->collectPOST();
        $return = TRUE;
        $error = array();
        if (!V::not_null($collectPOST->title)) {
            array_push($error, "يرجى إدخال عنوان مجلد");
            $return = FALSE;
        }
        $session = new Session();
        if (count($error) > 0) {
            $session->getFlashBag()->add('error', $error[0]);

            return $this->redirect($this->generateUrl('fe_menu_planner_show', array('slug' => $entity->getSeo()->getSlug())));
        }

        $entity->setTitle($collectPOST->title);
        $entity->setDescription($collectPOST->description);

        $em->persist($entity);
        $em->flush();

        $session->getFlashBag()->add('success', 'تم التعديل بنجاح');
        return $this->redirect($this->generateUrl('fe_menu_planner_show', array('slug' => $entity->getSeo()->getSlug())));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/copy-menu-planner/{slug}", name="fe_menu_planner_copy")
     * @Method("GET")
     * @Template("")
     */
    public function copyMenuPlannerAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->getDoctrine()->getManager();
        $menuPlanner = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);

        $entity = new MenuPlanner();

        $entity->setTitle($menuPlanner->getTitle());
        $entity->setDescription($menuPlanner->getDescription());

        $person = $em->getRepository('UserBundle:Person')->find($this->getUser()->getPerson()->getId());
        $entity->setPerson($person);
        $em->persist($entity);
        $em->flush();

        $seoEntity = new Seo();
        $seoEntity->setTitle($entity->getTitle());
        $seoEntity->setSlug('menu-planner/' . $entity->getId() . '-' . $entity->getTitle());
        $em->persist($seoEntity);
        $em->flush();

        $entity->setSeo($seoEntity);
        $em->persist($entity);
        $em->flush();

        foreach ($menuPlanner->getMenuPlannerHasRecipes() as $menuPlannerHasRecipe) {
            $menuPlannerHasRecipeNew = new MenuPlannerHasRecipe();
            $menuPlannerHasRecipeNew->setMenuPlanner($entity);
            $menuPlannerHasRecipeNew->setRecipe($menuPlannerHasRecipe->getRecipe());
            $menuPlannerHasRecipeNew->setCreated($menuPlannerHasRecipe->getCreated());
            $em->persist($menuPlannerHasRecipeNew);
        }
        $em->flush();

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم نقل صندوق الوصفات بنجاح');
        $this->getRequest()->getSession()->set('highlight', $entity->getId());
        return $this->redirect($this->generateUrl('fe_menu_planner_manage'));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/add-recipe/{slug}", name="fe_menu_planner_add_recipe")
     * @Method("POST")
     * @Template("")
     */
    public function addRecipeInMenuPlannerAjaxAction($slug) {
        if (!$this->getUser()) {
            $return = array('error' => 0);
        }

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('CMSBundle:Recipe')->findOneBySlug($slug);
        $menuPlannerId = $this->getRequest()->request->get('id');
        $menuPlannerIds = array();

        $personId = $this->getUser()->getPerson()->getId();

        if (V::not_null($menuPlannerId)) {
            if (is_numeric($menuPlannerId)) {
                $menuPlannerIds[] = $menuPlannerId;
            } else {
                $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->findBy(array('person' => $personId));
                foreach ($menuPlanners as $menuPlanner) {
                    $menuPlannerIds[] = $menuPlanner->getId();
                }
            }
            foreach ($menuPlannerIds as $menuPlannerId) {
                $menuPlannerHasRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('recipe' => $recipe->getId(), 'menuPlanner' => $menuPlannerId));
                if ($menuPlannerHasRecipe) {
                    $em->remove($menuPlannerHasRecipe);
                } else {
                    $menuPlanner = $em->getRepository('UserBundle:MenuPlanner')->find($menuPlannerId);
                    if ($menuPlanner AND $menuPlanner->getPerson()->getId() == $personId) {
                        $menuPlannerHasRecipe = new MenuPlannerHasRecipe();
                        $menuPlannerHasRecipe->setMenuPlanner($menuPlanner);
                        $menuPlannerHasRecipe->setRecipe($recipe);
                        $em->persist($menuPlannerHasRecipe);
                    }
                }
            }
            $em->flush();

            $return = array('error' => 0);
            return new Response(json_encode($return));
        }
    }

    /**
     * Lists all Package entities.
     *
     * @Route("/send-mail/{slug}", name="fe_menu_planner_send_mail")
     * @Method("POST")
     * @Template("")
     */
    public function sendMailAction($slug) {
        $em = $this->getDoctrine()->getManager();
        $menuPlanner = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);

        $email = $this->getRequest()->get('email');
        $msg = $this->getRequest()->get('message');

        $return = TRUE;
        $error = array();

        $referer = $this->getRequest()->headers->get('referer');
        if (!V::not_null($email)) {
            array_push($error, 'البريد الألكتروني');
            $return = FALSE;
        }
        if (V::not_null($email) AND ! V::email($email)) {
            array_push($error, 'بريد ألكتروني صحيح');
            $return = FALSE;
        }


        $session = new Session();
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
            $session->getFlashBag()->add('error', $return);
            return new RedirectResponse($referer);
        }


        $message = array(
            'subject' => 'صديق يريد مشاركة بعض الوصفات معك من أبلة طازة',
            'from' => 'info@ablatazza.com',
            'to' => array($email),
            'body' => $this->renderView(
                    'UserBundle:FrontEnd/MenuPlanner:menuPlannerEmail.html.twig', array(
                'menuPlanner' => $menuPlanner,
                'msg' => $msg,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        $session->getFlashBag()->add('success', 'تم إرسال رسالتك بنجاح');
        return new RedirectResponse($referer);
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete-menu-planner", name="fe_menu_planner_delete")
     * @Method("POST")
     */
    public function deleteAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:MenuPlanner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            throw $this->createNotFoundException('Access Denied.');
        }

        $em->remove($entity);
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم حذف المجلد بنجاح');
        return $this->redirect($this->generateUrl('fe_menu_planner_manage'));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete-menu-planner-recipe/{slug}", name="fe_menu_planner_recipe_delete")
     * @Method("POST")
     */
    public function deleteRecipeAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:MenuPlanner')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            throw $this->createNotFoundException('Access Denied.');
        }
        $menuPlannerHasRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('recipe' => $id, 'menuPlanner' => $entity->getId()));
        if ($menuPlannerHasRecipe) {
            $em->remove($menuPlannerHasRecipe);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('fe_menu_planner_show', array('slug' => $entity->getSeo()->getSlug())));
    }

    /**
     * Create a new Account entity.
     * @Route("/profile/{slug}/menu-planner/{page}", requirements={"page" = "\d+"}, name="fe_show_profile_menu_planner")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/MenuPlanner:viewProfileMenuPlanner.html.twig")
     */
    public function viewProfileMenuPlannerAction($slug, $page = 1) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);

        $search = new \stdClass;
        $search->userId = $person->getId();

        $menuPlannerCount = $em->getRepository('UserBundle:MenuPlanner')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($menuPlannerCount, $page, 8);
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());
        foreach ($menuPlanners as $menuPlanner) {
            $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
            if ($menuPlannerFirstRecipe) {
                $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
            } else {
                $menuPlanner->recipe = NULL;
            }
        }

        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId(), 4);

        return array(
            'person' => $person,
            'menuPlannerCount' => $menuPlannerCount,
            'menuPlanners' => $menuPlanners,
            'paginator' => $paginator->getPagination(),
            'followers' => $followers,
        );
    }

}
