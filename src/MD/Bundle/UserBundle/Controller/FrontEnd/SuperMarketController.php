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
use MD\Bundle\UserBundle\Entity\SuperMarket;
use Symfony\Component\HttpFoundation\Response;

/**
 * MenuPlanner controller.
 * @Route("/super-market")
 */
class SuperMarketController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/", name="fe_super_market")
     * @Method("GET")
     * @Template()
     */
    public function marketAction() {
        $em = $this->getDoctrine()->getManager();
        $ingredientCategories = $em->getRepository('CMSBundle:IngredientCategory')->findAll();
        $personId = $this->getUser()->getPerson()->getId();
        foreach ($ingredientCategories as $ingredientCategory) {
            $ingredientCategory->supermarkItems = $em->getRepository('UserBundle:SuperMarket')->findBy(array('person' => $personId, 'ingredientCategory' => $ingredientCategory->getId()), array('id' => 'DESC'));
        }

        return array(
            'ingredientCategories' => $ingredientCategories,
        );
    }

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/print", name="fe_super_market_print")
     * @Method("GET")
     * @Template()
     */
    public function printAction() {
        $em = $this->getDoctrine()->getManager();
        $ingredientCategories = $em->getRepository('CMSBundle:IngredientCategory')->findAll();
        $personId = $this->getUser()->getPerson()->getId();
        foreach ($ingredientCategories as $ingredientCategory) {
            $ingredientCategory->supermarkItems = $em->getRepository('UserBundle:SuperMarket')->findBy(array('person' => $personId, 'ingredientCategory' => $ingredientCategory->getId()), array('id' => 'DESC'));
        }

        return array(
            'ingredientCategories' => $ingredientCategories,
        );
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/add-item", name="fe_super_market_create")
     * @Method("POST")
     * @Template("")
     */
    public function addInIngredientCategoryAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $entity = new SuperMarket();

        $em = $this->getDoctrine()->getManager();

        $title = $this->getRequest()->request->get('title');
        $ingredientCategoryId = $this->getRequest()->request->get('id');
        $error = array();
        if (!V::not_null($title)) {
            array_push($error, "يرجى إدخال الأسم");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = $error[0];
            return new Response(json_encode(array('error' => 1, 'message' => $return)));
        }

        $entity->setTitle($title);

        $entity->setPerson($this->getUser()->getPerson());

        $ingredientCategory = $em->getRepository('CMSBundle:IngredientCategory')->find($ingredientCategoryId);
        if (!$ingredientCategory) {
            return new Response(json_encode(array('error' => 1, 'message' => 'خاطئ ')));
        }
        $entity->setIngredientCategory($ingredientCategory);
        $em->persist($entity);
        $em->flush();


        $return = array(
            'error' => 0,
            'message' => $this->renderView('UserBundle:FrontEnd/SuperMarket:superMarketItemAjax.html.twig', array(
                'entity' => $entity
            ))
        );
        return new Response(json_encode($return));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete", name="fe_super_market_delete")
     * @Method("POST")
     */
    public function deleteAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:SuperMarket')->find($id);

        if (!$entity) {
            return new Response(json_encode(array('error' => 1, 'message' => 'خاطئ')));
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            return new Response(json_encode(array('error' => 1, 'message' => 'تم الرفض')));
        }
        $em->remove($entity);
        $em->flush();
        return new Response(json_encode(array('error' => 0)));
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/change-state", name="fe_super_market_change_state")
     * @Method("POST")
     */
    public function changeStateAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('UserBundle:SuperMarket')->find($id);

        if (!$entity) {
            return new Response(json_encode(array('error' => 1, 'message' => 'خاطئ')));
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            return new Response(json_encode(array('error' => 1, 'message' => 'تم الرفض')));
        }

        if ($entity->getChecked() == TRUE) {
            $entity->setChecked(FALSE);
            $state = 0;
        } else {
            $entity->setChecked(TRUE);
            $state = 1;
        }

        $em->flush();
        return new Response(json_encode(array('error' => 0, 'message' => $state)));
    }

    /**
     * Lists all Package entities.
     *
     * @Route("/send-mail", name="fe_super_market_send_mail")
     * @Method("POST")
     * @Template("")
     */
    public function sendMailAction() {
        $em = $this->getDoctrine()->getManager();

        $email = $this->getRequest()->get('email');
        $msg = $this->getRequest()->get('message');

        $return = TRUE;
        $error = array();

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
            return $this->redirect($this->generateUrl('fe_super_market'));
        }


        $ingredientCategories = $em->getRepository('CMSBundle:IngredientCategory')->findAll();
        $personId = $this->getUser()->getPerson()->getId();
        foreach ($ingredientCategories as $ingredientCategory) {
            $ingredientCategory->supermarkItems = $em->getRepository('UserBundle:SuperMarket')->findBy(array('person' => $personId, 'ingredientCategory' => $ingredientCategory->getId()), array('id' => 'DESC'));
        }

        $message = array(
            'subject' => 'صديق يريد مشاركة بعض المكونات معك من أبلة طازة',
            'from' => 'info@ablatazza.com',
            'to' => array($email),
            'body' => $this->renderView(
                    'UserBundle:FrontEnd/SuperMarket:superMarketEmail.html.twig', array(
                'ingredientCategories' => $ingredientCategories,
                'msg' => $msg,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        $session->getFlashBag()->add('success', 'تم إرسال رسالتك بنجاح');
        return $this->redirect($this->generateUrl('fe_super_market'));
    }

}
