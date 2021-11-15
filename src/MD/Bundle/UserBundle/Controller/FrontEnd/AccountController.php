<?php

namespace MD\Bundle\UserBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \MD\Bundle\UserBundle\Entity\Account;
use \MD\Bundle\UserBundle\Entity\Person;
use MD\Bundle\UserBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use MD\Utils\Validate as V;
use MD\Utils\OAuth;
use \MD\Bundle\MediaBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Entity\Seo;
use MD\Bundle\CMSBundle\Lib\Paginator;

/**
 * Account controller.
 * @Route("")
 */
class AccountController extends Controller {

    /**
     * @Route("/login", name="fe_login")
     */
    public function loginAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') or $this->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('fe_home'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans("Home"), $this->get("router")->generate("fe_home"));
        $breadcrumbs->addItem($this->get('translator')->trans("Login"));

// get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                    SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
                        'FEBundle::login.html.twig', array(
// last username entered by the user
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
                        )
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/change-password", name="fe_pre_change_password")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:changePassword.html.twig")
     */
    public function preChangePasswordAction() {
        return array(
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/change-password", name="fe_change_password")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Usr:changePassword.html.twig")
     */
    public function changePasswordAction() {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $nPassword = $this->getRequest()->get('n_password');
        $rPassword = $this->getRequest()->get('r_password');
        if ($rPassword == $nPassword) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($nPassword, $user->getSalt());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();
            $token = new UsernamePasswordToken(
                    $user, null, 'members_secured_area', array('ROLE_USER')
            );
            $securityContext = $this->container->get('security.context');
            $securityContext->setToken($token);
            $session = $this->get('session');
            $session->set('_security_' . 'members_secured_area', serialize($token));

            $this->getRequest()->getSession()->getFlashBag()->add('success', 'لقد تم تغير كلمة السر');
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', 'كلمة السر لا تطابق تأكد كلمة السر');
        }

        return array(
//            'message' => $Message
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/forgot-password", name="fe_pre_forgot_password")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:forgot.html.twig")
     */
    public function preForgotPasswordAction() {
        return array(
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/forgot-password", name="fe_forgot_password")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Usr:forgot.html.twig")
     */
    public function forgotAction() {
        $em = $this->getDoctrine()->getManager();
        $email = $this->getRequest()->get('email');
        $user = $em->getRepository('UserBundle:Account')->getAccountByUSername($email);
        if ($user) {
            $code = md5($user->getPerson()->getId());

            $message = array(
                'subject' => 'Forgot the password',
                'from' => 'info@ablatazza.com',
                'to' => array($email),
                'body' => $this->renderView(
                        'UserBundle:FrontEnd/Usr:forgotEmail.html.twig', array(
                    'verifyCode' => $code
                        )
                )
            );
            \MD\Utils\Mailer::sendEmail($message);


            $this->getRequest()->getSession()->getFlashBag()->add('success', "Please check your email");
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('error', "This email is not exist");
        }
        return array(
        );
    }

    /**
     * @Route("/email/forgotPassword/{code}", name="fe_forgot_email")
     */
    public function forgotPasswordVerifyAction($code) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:Account")->getSelectedAccount($code, Account::BLOCKED);
        if ($user) {
            $user->setState(Account::VERIFIED);
            $em->persist($user);
            $em->flush();
            $token = new UsernamePasswordToken(
                    $user, null, 'members_secured_area', array('ROLE_USER')
            );
            $securityContext = $this->container->get('security.context');
            $securityContext->setToken($token);
            $session = $this->get('session');
            $session->set('_security_' . 'members_secured_area', serialize($token));
            return $this->redirect($this->generateUrl('fe_pre_change_password'));
        } else {
            exit("You are blocked  ");
        }
    }

    /**
     * @Route("/facebook-request", name="facebook_request")
     */
    public function facebookRequestAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $client_id = '213035072372119';
        $client_secret = '0ccd9ed6cd8add40537b52aee9b9189b';
        $redirect_uri = $this->generateUrl('facebook_request', array(), TRUE);
        $scope = "email,user_birthday";
        $response_type = 'token';
        $approval_prompt = 'force';
        $access_type = 'online';
        $previousRoute = $this->getRequest()->headers->get('referer');
        $oauth2token_url = "https://graph.facebook.com/oauth/access_token";
        $OAuth = new OAuth($client_id, $client_secret, $redirect_uri, $scope, $response_type);

        $code = $request->get('code');
        if (isset($code)) {
            $session->set('accessToken', $OAuth->get_oauth2_token($request->get('code'), $oauth2token_url));
        }
        $accessToken = $session->get('accessToken');
        if (isset($accessToken)) {
            $accountObj = $OAuth->call_api($session->get('accessToken'), "https://graph.facebook.com/me", 'name,email', 'f');

            if (!isset($accountObj->email)) {
                $this->getRequest()->getSession()->getFlashBag()->add('error', 'حساب الفيسبوك لديك لا يوجد به إيميل أساسي');
                return $this->redirect($this->generateUrl('login'));
            }

            $email = filter_var($accountObj->email, FILTER_SANITIZE_EMAIL);
            $name = filter_var($accountObj->name, FILTER_SANITIZE_STRING);
            $fbId = filter_var($accountObj->id, FILTER_SANITIZE_STRING);
            $session->set('full_name', $name);
            $session->set('email', $email);
            $session->set('fb_id', $fbId);
            $session->set('access', 'facebook');
            return $this->redirect($this->generateUrl('fe_facebook_create', array("route" => $previousRoute)));
        }
        if (!(isset($code) || isset($accessToken))) {
            $loginUrl = sprintf("https://www.facebook.com/dialog/oauth?scope=" . $scope . "&redirect_uri=" . $redirect_uri . "&client_id=" . $client_id);
            return $this->redirect($loginUrl);
        }
    }

    /**
     * Create a new Account using Facebook entity.
     *
     * @Route("/facebook", name="fe_facebook_create")
     */
    public function facebookCreateAction() {
        $em = $this->getDoctrine()->getManager();
        $userPostData = $this->facebookCollectPost();
        $oldUser = $em->getRepository('UserBundle:Account')->getAccountByUSername($userPostData->email);
        $route = $this->generateUrl('fe_home');
//        $route = $this->getRequest()->get('route');
        if ($oldUser) {
            $oldUser->setState(Account::VERIFIED);
            $em->persist($oldUser);
            $em->flush();
            $userFb = $oldUser->getUserFacebook();
            if ($userFb) {
                $userFb->setFacebookId($userPostData->facebookId);
                $em->persist($userFb);
            } else {
                $newFbUser = new \MD\Bundle\UserBundle\Entity\userFaceBook();
                $newFbUser->setFacebookId($userPostData->facebookId);
                $newFbUser->setId($oldUser);
                $em->persist($newFbUser);
            }
            $em->flush();
            return $this->doLogin($oldUser, $route);
        } else {
            $validatePersonResponse = $this->addPerson($userPostData);
            $responseData = json_decode($validatePersonResponse->getContent());
            if ((int) $responseData->valid == -1) {
                $errorMessage = "Sorry you already use your facebook mail to regiester  ";
                $session = new Session();
                $session->getFlashBag()->add('error', $errorMessage);
            } else if ((int) $responseData->valid == -2) {
                $errorMessage = "Sorry please review the data you have enter and fill all the required fields ";
                $session = new Session();
                $session->getFlashBag()->add('error', $errorMessage);
            } else {
                $person = unserialize($responseData->person);
                $newUser = new Account();
                $newFbUser = new \MD\Bundle\UserBundle\Entity\userFaceBook();
                $newUser->setState(Account::VERIFIED);
                $newUser->setUsername($userPostData->email);
                $newUser->setPerson($person);
                $em->persist($newUser);
                $em->flush();

                $seo = new Seo;
                $seo->setTitle($person->getName());
                $seo->setSlug('person/' . $person->getName() . '-' . $person->getId());
                $person->setSeo($seo);
                $em->persist($person);
                $em->flush();

                $newFbUser->setFacebookId($userPostData->facebookId);
                $newFbUser->setId($newUser);
                $em->persist($newFbUser);
                $em->flush();
                $roleUser = $em->getRepository('UserBundle:Role')->findOneBy(array("name" => "ROLE_USER"));
                $roleUser->addAccount($newUser);
                $em->persist($roleUser);
                $em->flush();
                $aclProvider = $this->get('security.acl.provider');
                $objectIdentity = ObjectIdentity::fromDomainObject($newUser);
                $acl = $aclProvider->createAcl($objectIdentity);

// retrieving the security identity of the currently logged-in user
                $securityIdentity = UserSecurityIdentity::fromAccount($newUser);

// grant owner access
                $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
                $aclProvider->updateAcl($acl);
                return $this->doLogin($newUser, $route);
            }
            return $this->redirect($this->generateUrl('login'));
        }
    }

    /**
     *  Loged the user in  
     */
    public function doLogin($user, $route = null) {
        $token = new UsernamePasswordToken(
                $user, null, 'members_secured_area', array('ROLE_USER')
        );
        $securityContext = $this->container->get('security.context');
        $securityContext->setToken($token);
        $session = $this->get('session');
        $session->set('_security_' . 'members_secured_area', serialize($token));
//        var_dump($session->get('_security_' . 'members_secured_area'));
        if ($route != null && $route != "http://theholidayers_prod_2.mdapp.me/app_dev.php/login") {
            $newResponse = new RedirectResponse($route);
            return $newResponse;
        }
        return $this->redirect($this->generateUrl('fe_my_profile'));
    }

    public function facebookCollectPost() {
        $user = new \stdClass();
//        echo var_dump($_SESSION['_sf2_attributes']);
//        exit;
        $user->firstName = $_SESSION['_sf2_attributes']['full_name'];
        $user->email = $_SESSION['_sf2_attributes']['email'];
        $user->facebookId = $_SESSION['_sf2_attributes']['fb_id'];
        return $user;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/sign-up", name="fe_user_new")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:new.html.twig")
     */
    public function newAction() {
        $em = $this->getDoctrine()->getManager();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($this->get('translator')->trans("Home"), $this->get("router")->generate("fe_home"));
        $breadcrumbs->addItem($this->get('translator')->trans("Register"));
        $entity = new Account();
        return array(
            'entity' => $entity,
        );
    }

    /**
     * Create a new Account using Facebook entity.
     * @Route("/new", name="fe_usr_create")
     */
    public function createAction() {
        $errorMessage = "";
        $personPostData = $this->collectPOST();
        $validatePersonResponse = $this->addPerson($personPostData);
        $responseData = json_decode($validatePersonResponse->getContent());

        $session = new Session();

        if ((int) $responseData->valid == -1) {
            $session->getFlashBag()->add('error', 'عذرا البريد الإلكتروني الذي قمت بإدخاله مستخدم بالفعل.');
        } else if ((int) $responseData->valid == -2) {
            $session->getFlashBag()->add('error', 'عذرا يرجى مراجعة البيانات التي قمت بإدخالها وملء جميع الحقول المطلوبة.');
		 } else if ((int) $responseData->valid == -3) {
			$session->getFlashBag()->add('error', 'Invalid Captcha');
        } else {
            $person = unserialize($responseData->person);
            $this->addAccount($personPostData, $person);
            $code = md5($person->getId());

            $message = array(
                'subject' => 'User Verfication',
                'from' => 'info@ablatazza.com',
                'to' => array($person->getEmail()),
                'body' => $this->renderView(
                        'UserBundle:FrontEnd/Usr:VerifyEmail.html.twig', array(
                    'user' => $person,
                    'verifyCode' => $code
                        )
                )
            );
            \MD\Utils\Mailer::sendEmail($message);

            return $this->render(
                            'UserBundle:FrontEnd/Usr:thank.html.twig', array(
                        'verifyCode' => $code,
                        'userId' => $person->getId(),
                            )
            );
        }
        return $this->render("UserBundle:FrontEnd/Usr:new.html.twig", array('data' => $personPostData));
    }

    /**
     * Validate Person entity
     * -1 => the email is used in the database
     * -2 => the required data is not complete
     */
    public function validatePerson($personPostData) {
        $person = new Person();
        $person->setFirstName($personPostData->firstName);
        if (isset($personPostData->familyname)) {
            $person->setFamilyname($personPostData->familyname);
        }
        $person->setEmail($personPostData->email);
        if (isset($personPostData->gender)) {
            $person->setGender($personPostData->gender);
        }
        if (isset($personPostData->phone)) {
            $person->setPhone($personPostData->phone);
        }
        if (isset($personPostData->birthdate) AND V::not_null($personPostData->birthdate) AND V::date($personPostData->birthdate, 'YYYY-MM-DD')) {
            $d1 = new \DateTime($personPostData->birthdate);
            $person->setBirthdate($d1);
        }
        $validator = $this->get('validator');
        $errors = $validator->validate($person);
        $em = $this->getDoctrine()->getManager();
        $validateEmail = $em->getRepository('UserBundle:Account')->findOneByUsername($personPostData->email);
        if ($validateEmail) {
            $returnArray = array("person" => null, 'valid' => -1);
            $returnData = json_encode($returnArray);
            return new Response($returnData);
        }
        if (count($errors) > 0) {
            $returnArray = array("person" => null, 'valid' => -2);
            $returnData = json_encode($returnArray);
            return new Response($returnData);
        }
		$reCaptcha = new \MD\Utils\ReCaptcha();
        $reCaptchaValidate = $reCaptcha->verifyResponse();
        
        if ($reCaptchaValidate->success == False) {
            $returnArray = array("person" => null, 'valid' => -3);
            $returnData = json_encode($returnArray);
            return new Response($returnData);
        }
        $returnArray = array("person" => serialize($person), 'valid' => 1);
        $returnData = json_encode($returnArray);
        return new Response($returnData);
    }

    /**
     * Add New Person
     */
    public function addPerson($personPostData) {
        $em = $this->getDoctrine()->getManager();
        $response = $this->validatePerson($personPostData);
        $responseData = json_decode($response->getContent());
        if ((int) $responseData->valid < 0) {
            return new Response(json_encode($responseData));
        }
        $person = unserialize($responseData->person);

//        $em->persist($person);
//        $em->flush();

        $returnArray = array("person" => serialize($person), 'valid' => 1);
        $returnData = json_encode($returnArray);
        return new Response($returnData);
    }

    /**
     * Add New Account
     */
    public function addAccount($personPostData, $person) {
        $em = $this->getDoctrine()->getManager();
        $account = new Account();
        $account->setUsername($personPostData->email);
        $account->setPassword($personPostData->password);
        $account->setState(Account::NOT_VERIFIED);
//        $em->persist($account);
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($account);
        $password = $encoder->encodePassword($account->getPassword(), $account->getSalt());
        $account->setPassword($password);
        $account->setPerson($person);
        $em->persist($account);
        $em->flush();

        $seo = new Seo;
        $seo->setTitle($person->getFirstName() . ' ' . $person->getFamilyname());
        $seo->setSlug('person/' . $person->getFirstName() . '-' . $person->getFamilyname() . '-' . $person->getId());
        $person->setSeo($seo);
        $em->persist($person);
        $em->flush();


        $roleUser = $em->getRepository('UserBundle:Role')->find(Role::ROLE_USER);
        $roleUser->addAccount($account);
        $em->persist($roleUser);
        $em->flush();
// creating the ACL
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($account);
        $acl = $aclProvider->createAcl($objectIdentity);

// retrieving the security identity of the currently logged-in user
        $securityIdentity = UserSecurityIdentity::fromAccount($account);

// grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);
        return $account;
    }

    /**
     * Create a new Account entity.
     *
     * @Route("/resend-email/{code}/{userId}", name="fe_user_resend_email")
     */
    public function sendEmailAction() {
        $code = $this->getRequest()->get('code');
        $userId = $this->getRequest()->get('userId');
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('UserBundle:Person')->find($userId);

        $message = array(
            'subject' => 'User Verfication',
            'from' => 'info@ablatazza.com',
            'to' => array($person->getEmail()),
            'body' => $this->renderView(
                    'UserBundle:FrontEnd/Usr:VerifyEmail.html.twig', array(
                'user' => $person,
                'verifyCode' => $code
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        $this->getRequest()->getSession()->getFlashBag()->add('success', "أرسلت رسالة ثانية يرجى التحقق من بريدك الالكتروني");

        return $this->render(
                        'UserBundle:FrontEnd/Usr:thank.html.twig', array(
                    'verifyCode' => $code,
                    'userId' => $person->getId(),
                        )
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/follow/{slug}", name="fe_follow_user")
     * @Method("POST")
     * @Template()
     */
    public function followAction($slug) {
        $em = $this->getDoctrine()->getManager();
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);
        $follower = $this->getUser()->getPerson();

        $isFollow = $em->getRepository('UserBundle:Follower')->findOneBy(array('person' => $person->getId(), 'follower' => $follower->getId()));
        if ($isFollow) {
            $return = 0;
            $em->remove($isFollow);
        } else {
            $followerEntity = new \MD\Bundle\UserBundle\Entity\Follower();
            $followerEntity->setPerson($person);
            $followerEntity->setFollower($follower);
            $em->persist($followerEntity);
            $return = 1;
        }
        $em->flush();
        return new Response($return);
    }

    /**
     * Create a new Account entity.
     * @Route("/profile/{slug}", name="fe_show_profile")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:viewProfile.html.twig")
     */
    public function viewProfileAction($slug) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);

        $search = new \stdClass;
        $search->userId = $person->getId();
        $search->draft = 0;
        $search->publish = 1;

        $recipeCount = $em->getRepository('CMSBundle:Recipe')->filter($search, TRUE);
        $paginator = new Paginator($recipeCount, 1, 6);
        $recipes = $em->getRepository('CMSBundle:Recipe')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $articleCount = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $paginator = new Paginator($articleCount, 1, 6);
        $articles = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $tipCount = $em->getRepository('CMSBundle:Tip')->filter($search, TRUE);
        $paginator = new Paginator($tipCount, 1, 6);
        $tips = $em->getRepository('CMSBundle:Tip')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId(), 4);

        $menuPlannerCount = $em->getRepository('UserBundle:MenuPlanner')->filter($search, TRUE);
        $paginator = new Paginator($tipCount, 1, 6);
        $menuPlanners = $em->getRepository('UserBundle:MenuPlanner')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());
        foreach ($menuPlanners as $menuPlanner) {
            $menuPlannerFirstRecipe = $em->getRepository('UserBundle:MenuPlannerHasRecipe')->findOneBy(array('menuPlanner' => $menuPlanner->getId()), array('created' => 'ASC'));
            if ($menuPlannerFirstRecipe) {
                $menuPlanner->recipe = $menuPlannerFirstRecipe->getRecipe();
            } else {
                $menuPlanner->recipe = NULL;
            }
        }

        return array(
            'person' => $person,
            'recipeCount' => $recipeCount,
            'recipes' => $recipes,
            'articleCount' => $articleCount,
            'articles' => $articles,
            'tipCount' => $tipCount,
            'tips' => $tips,
            'menuPlanners' => $menuPlanners,
            'menuPlannerCount' => $menuPlannerCount,
            'followers' => $followers,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/follower/{slug}", name="fe_show_follower")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:viewFollower.html.twig")
     */
    public function viewFollowerAction($slug) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);
        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId());

        return array(
            'person' => $person,
            'followers' => $followers,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/my-profile", name="fe_my_profile")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:edit.html.twig")
     */
    public function myProfileAction() {

        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $message = null;

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $user = $em->getRepository('UserBundle:Account')->find($user->getId());

        $form = $this->createForm(new \MD\Bundle\UserBundle\Form\PersonType());

        return array(
            'user' => $user,
            'form' => $form->createView(),
        );
    }

    /**

      /**
     * Create a new Account entity.
     * @Route("/update-my-profile", name="fe_usr_update_my_profile")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Usr:edit.html.twig")
     */
    public function updateMyProfileAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
        $formView = $uploadForm->createView();
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $file = $data_upload["file"];
        $user = $this->get('security.context')->getToken()->getUser();
        $usr = $em->getRepository('UserBundle:Account')->find($user->getId());

        if (!$usr) {
            throw $this->createNotFoundException('Unable to find this user ');
        }
        $usrPostData = $this->collectPOST();

        $usr->getPerson()->setFirstName($usrPostData->firstName);
        $usr->getPerson()->setFamilyname($usrPostData->familyname);
        $usr->getPerson()->setEmail($usrPostData->email);
        $usr->setUsername($usrPostData->email);

        $usr->getPerson()->setGender($this->getRequest()->get('gender'));
        $usr->getPerson()->setAddress($usrPostData->address);
        $usr->getPerson()->setPhone($usrPostData->phone);
        if (isset($usrPostData->birthdate) AND V::not_null($usrPostData->birthdate) AND V::date($usrPostData->birthdate, 'YYYY-MM-DD')) {
            $d1 = new \DateTime($usrPostData->birthdate);
            $usr->getPerson()->setBirthdate($d1);
        }

        $imageController = new \MD\Bundle\MediaBundle\Controller\ImageController();
        $imageController->uploadSingleImage($em, $usr->getPerson(), $file, 3);

        $em->persist($usr->getPerson());
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', " تم تعديل بياناتك بنجاح");

        return $this->redirect($this->generateUrl('fe_my_profile'));
    }

    protected function collectPOST() {
        $request = $this->getRequest()->request;
        $user = new \stdClass();
        $user->firstName = $request->get('firstName');
        $user->familyname = $request->get('familyname');
        $user->address = $request->get('address');
        $user->city = $request->get('city');
        $user->password = $request->get('password');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->gender = $request->get('gender');
        $user->birthdate = $request->get('birthdate');
        return $user;
    }

    /**
     *  manage date to insert 
     * @param type $day
     * @param type $month
     * @param type $year
     * @return boolean
     */
    public function mangeDateFormatAndValidate($day, $month, $year) {
        if ($day != NULL && $month != NULL && $year != NULL)
            return $birthDateFormat = $day . "-" . $month . "-" . $year;
        else
            return FALSE;
    }

    /**
     * @Route("/email/verify/{code}", name="fe_email_verify")
     */
    public function emailVerifyAction($code) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("UserBundle:Account")->getSelectedAccount($code, Account::BLOCKED);
        if ($user) {
            $user->setEmailVerify(Account::VERIFIED);
            $user->setState(Account::VERIFIED);
            $em->persist($user);
            $em->flush();
            return $this->doLogin($user);
        } else {
            exit("You are blocked ");
        }
    }

    /**
     * Creates a new person image.
     *
     * @Route("/gallery/{id}" , name="person_create_images")
     * @Method("POST")
     * @Template("UserBundle:Person:GetImages.html.twig")
     */
    public function SetImagesAction(Request $request, $id) {
        $form = $this->createForm(new \MD\Bundle\MediaBundle\Form\ImageType());
        $form->bind($request);

        $data = $form->getData();
        $files = $data["files"];

        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('UserBundle:Person')->find($id);
        if (!$person) {
            throw $this->createNotFoundException('Unable to find package entity.');
        }
        $imageType = $request->get("type");
        foreach ($files as $file) {
            if ($file != NULL) {
                $image = new Image();
                $em->persist($image);
                $em->flush();
                $image->setFile($file);
                $logoImages = $person->getImages(array(\MD\Bundle\MediaBundle\Entity\Image::TYPE_LOGO));
                if ($imageType == Image::TYPE_LOGO && count($logoImages) > 0) {
                    foreach ($logoImages As $logo) {
                        $this->deleteOldImage($logo, $person, $image);
                        $image->setImageType(Image::TYPE_LOGO);
                    }
                } else if ($imageType == \MD\Bundle\MediaBundle\Entity\Image::TYPE_LOGO && count($logoImages) == 0) {
                    $image->setImageType(Image::TYPE_LOGO);
                } else {
                    $image->setImageType(Image::TYPE_GALLERY);
                }
                $image->preUpload();
                $image->upload("person/" . $id);
                $person->addImage($image);
                $imageUrl = "/uploads/person/" . $id . "/" . $image->getId();
                $imageId = $image->getId();
            }

            $em->persist($person);
            $em->flush();
        }
        $files = '{"files":[{"url":"' . $imageUrl . '","thumbnailUrl":"http://lh6.ggpht.com/0GmazPJ8DqFO09TGp-OVK_LUKtQh0BQnTFXNdqN-5bCeVSULfEkCAifm6p9V_FXyYHgmQvkJoeONZmuxkTBqZANbc94xp-Av=s80","name":"test","id":"' . $imageId . '","type":"' . $image->getExtension() . '","size":620888,"deleteUrl":"http://localhost/packagat/web/uploads/packages/1/th71?delete=true","deleteType":"DELETE"}]}';
        $response = new Response();
        $response->setContent($files);
        $response->setStatusCode(200);
        return $response;
    }

    public function deleteOldImage($logo, $person, $image) {
        $em = $this->getDoctrine()->getManager();
        $person->removeImage($logo);
        $em->persist($person);
        $em->flush();
        $image->storeFilenameForRemove("person/" . $person->getId());
        $image->removeUpload();
//        $image->storeFilenameForResizeRemove("person/" . $person->getId());
//        $image->removeResizeUpload();
        $em->persist($logo);
        $em->flush();
        $em->remove($logo);
        $em->flush();
        return true;
    }

    /**
     * Deletes a AttractionGallery entity.
     *
     * @Route("/deleteimage/{h_id}/{redirect_id}", name="personimages_delete")
     * @Method("POST")
     */
    public function deleteImageAction($h_id, $redirect_id) {
        $image_id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:Person')->find($h_id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find person entity.');
        }
        $image = $em->getRepository('MediaBundle:Image')->find($image_id);
        if (!$image) {
            throw $this->createNotFoundException('Unable to find image entity.');
        }
        $entity->removeImage($image);
        $em->persist($entity);
        $em->flush();
        $image->storeFilenameForRemove("person/" . $h_id);
        $image->removeUpload();
//        $image->storeFilenameForResizeRemove("person/" . $h_id);
//        $image->removeResizeUpload();
        $em->persist($image);
        $em->flush();
        $em->remove($image);
        $em->flush();
        return $this->redirect($this->generateUrl('fe_my_profile', array('id' => $this->getUser()->getId())));
    }

    /**
     * Deletes a AttractionGallery entity.
     *
     * @Route("/my-profile/delete-image", name="personimages_my_profile_delete")
     * @Method("POST")
     */
    public function deleteMyProfileImageAction() {
        $h_id = $user = $this->getUser()->getPerson()->getId();
        $image_id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:Person')->find($h_id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find person entity.');
        }
        $image = $entity->getImage();
        if (!$image) {
            throw $this->createNotFoundException('Unable to find image entity.');
        }
        $entity->removeImage($image);
        $em->persist($entity);
        $em->flush();
        $image->storeFilenameForRemove("person/" . $h_id);
        $image->removeUpload();
//        $image->storeFilenameForResizeRemove("person/" . $h_id);
//        $image->removeResizeUpload();
        $em->persist($image);
        $em->flush();
        $em->remove($image);
        $em->flush();
        return $this->redirect($this->generateUrl('fe_my_profile'));
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/tabs", name="fe_tabs")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Usr:tabs.html.twig")
     */
    public function tabsAction() {
        $em = $this->getDoctrine()->getManager();
        return array();
    }

    /**
     * Create a new Account entity.
     * @Route("/my-favorite", name="fe_my_favorite")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Usr:myFavorite.html.twig")
     */
    public function myFavoriteAction() {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }
        $personId = $this->getUser()->getPerson()->getId();
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('CMSBundle:Article')->getFavArticleByUserId($personId);
        $recipes = $em->getRepository('CMSBundle:Recipe')->getFavRecipeByUserId($personId);
        $fadfadas = $em->getRepository('CMSBundle:Fadfada')->getFavFadfadaByUserId($personId);


        return array(
            "articles" => $articles,
            "recipes" => $recipes,
            "fadfadas" => $fadfadas,
        );
    }

}
