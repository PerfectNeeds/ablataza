<?php

namespace MD\Bundle\UserBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MD\Utils\Validate as V;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Tip controller.
 * @Route("/tip")
 */
class TipController extends Controller {

    /**
     * Create a new Account entity.
     * @Route("/profile/{slug}/tip/{page}", requirements={"page" = "\d+"}, name="fe_show_profile_tip")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Tip:viewProfileTip.html.twig")
     */
    public function viewProfileTipAction($slug, $page = 1) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);

        $search = new \stdClass;
        $search->userId = $person->getId();
        $search->draft = 0;
        $search->publish = 1;

        $tipCount = $em->getRepository('CMSBundle:Tip')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($tipCount, $page, 8);
        $tips = $em->getRepository('CMSBundle:Tip')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId(), 4);

        return array(
            'person' => $person,
            'tipCount' => $tipCount,
            'tips' => $tips,
            'paginator' => $paginator->getPagination(),
            'followers' => $followers,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/my", name="fe_my_tip")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Tip:myTip.html.twig")
     */
    public function myTipAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('CMSBundle:Tip')->findBy(array('person' => $this->get('security.context')->getToken()->getUser()->getPerson()->getId()));
        return array(
            "entities" => $entities,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/add", name="fe_add_tip")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Tip:addTip.html.twig")
     */
    public function addTipAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('deleted' => FALSE));
        return array(
            "superCategories" => $superCategories
        );
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete", name="fe_tip_delete")
     * @Method("POST")
     */
    public function deleteTipAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Tip')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            throw $this->createNotFoundException('Access Denied.');
        }

        $em->remove($entity);
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم الحذف بنجاح');
        return $this->redirect($this->generateUrl('fe_my_tip'));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/create", name="fe_tip_create")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Tip:addTip.html.twig")
     */
    public function createTipAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $entity = new \MD\Bundle\CMSBundle\Entity\Tip();


        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->get('data');

        $return = TRUE;
        $error = array();
        if (!V::not_null($data['text'])) {
            array_push($error, "text");
            $return = FALSE;
        }
        if (!V::not_null($data['superCategory'])) {
            array_push($error, "super Category");
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
            $session->getFlashBag()->add('errorTip', $return);

            return $this->redirect($this->generateUrl('fe_add_tip'));
        }

        $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($data['superCategory']);

        $entity->setSuperCategory($superCategory);
        $entity->setTip($data['text']);
        $entity->setPublish(False);

        $person = $em->getRepository('UserBundle:Person')->find($this->getUser()->getPerson()->getId());
        $entity->setPerson($person);
        $em->persist($entity);
        $em->flush();

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'شكرا نصحتك وصل لأبلة طازة وهتنزله لحبايبها بعد مراجعته شاركينا أكتر بنصائحك ');

        return $this->redirect($this->generateUrl('fe_my_tip'));
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", name="fe_tip_edit")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Tip:editTip.html.twig")
     */
    public function editTipAction($id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Tip')->find($id);
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('deleted' => FALSE));
        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }


        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
        );
    }

    /**
     * Edits an existing Tip entity.
     *
     * @Route("/update/{id}", name="fe_tip_update")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Tip:editTip.html.twig")
     */
    public function updateTipAction($id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Tip')->find($id);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tip entity.');
        }

        $data = $this->getRequest()->request->get('data');
        $return = TRUE;

        $error = array();
        if (!V::not_null($data['text'])) {
            array_push($error, "text");
            $return = FALSE;
        }
        if (!V::not_null($data['superCategory'])) {
            array_push($error, "super Category");
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

            return $this->redirect($this->generateUrl('fe_tip_edit', array('slug' => $entity->getSeo()->getSlug())));
        }

        $superCategory = $em->getRepository('CMSBundle:SuperCategory')->find($data['superCategory']);

        $entity->setSuperCategory($superCategory);
        $entity->setTip($data['text']);

        $em->persist($entity);
        $em->flush();

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم التعديل بنجاح');
        return $this->redirect($this->generateUrl('fe_my_tip', array('id' => $entity->getId())));
    }

}
