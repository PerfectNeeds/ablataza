<?php

namespace MD\Bundle\UserBundle\Controller\Administration;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use \MD\Bundle\UserBundle\Entity\Account;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Account controller.
 *
 * @Route("/user")
 */
class AccountController extends Controller {

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/List", name="user_list")
     * @Method("GET")
     * @Template("UserBundle:Administration/Account:index.html.twig")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('UserBundle:Account')->findAll();
        return array(
            'users' => $users
        );
    }

    /**
     * Deletes a Supplier entity.
     *
     * @Route("/user-block", name="user_block")
     * @Method("POST")
     */
    public function blockAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:Account')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }
        $entity->setState(0);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('person_user'));
    }

    /**
     * Deletes a Supplier entity.
     *
     * @Route("/user-active", name="user_active")
     * @Method("POST")
     */
    public function activeAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:Account')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }
        $entity->setState(1);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('person_user'));
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template("UserBundle:Administration/Account:new.html.twig")
     */
    public function newAction() {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $entity = new Account();
        $error = "";
        return array(
            'entity' => $entity,
            'errorMessage' => $error
        );
    }

    /**
     * Create a new Account using Facebook entity.
     * @Route("/new", name="account_create")
     */
    public function createAction() {
        if (!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();

        $accountPostData = $this->collectPOST();
        $validateEmail = $em->getRepository('UserBundle:Account')->getUserByEmail($accountPostData->email);
        if ($validateEmail) {
            return $this->render("UserBundle:Administration/Account:new.html.twig", array(
                        "errorMessage" => "sorry u can't use this email beacuse it's already taken  "
            ));
        } else {
            $account = new Account();
            $account->setEmail($accountPostData->email);
            $account->setUsername($accountPostData->username);
            $account->setPassword($accountPostData->password);
            $account->setGender($accountPostData->gender);
            $account->setState($accountPostData->state);
            $em->persist($account);
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($account);
            $password = $encoder->encodePassword($account->getPassword(), $account->getSalt());
            $account->setPassword($password);
            $em->persist($account);
            $em->flush();
            $roleUser = $em->getRepository('UserBundle:Role')->findOneBy(array("name" => "ROLE_ADMIN"));
            $roleUser->addUser($account);
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
            return $this->redirect($this->generateUrl('user_list'));
        }
    }

    protected function collectPOST() {
        $user = new \stdClass();
        $user->username = $this->getRequest()->get('username');
        $user->password = $this->getRequest()->get('password');
        $user->email = $this->getRequest()->get('email');
        $user->phone = $this->getRequest()->get('phone');
        $user->gender = $this->getRequest()->get('gender');
        $user->state = $this->getRequest()->get('active');
        $day = $this->getRequest()->get('day');
        $month = $this->getRequest()->get('month');
        $year = $this->getRequest()->get('year');
        $birthDateFormat = $day . "-" . $month . "-" . $year;
        $user->birthdate = $birthDateFormat;
        if ($this->getRequest()->get('monthlyFeeds'))
            $user->monthlyFeeds = $this->getRequest()->get('monthlyFeeds');
        else
            $user->monthlyFeeds = \MD\Bundle\UserBundle\Entity\Account::UNSUBSCRIBED;
        return $user;
    }

}
