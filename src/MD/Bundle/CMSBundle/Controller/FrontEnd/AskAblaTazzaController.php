<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Ask;
use MD\Utils\Validate as V;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * WhatWeDo controller.
 *
 * @Route("/ask-ablatazza")
 */
class AskAblaTazzaController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/{page}", requirements={"page" = "\d+"}, name="fe_ask-ablatazza")
     * @Method("GET")
     * @Template()
     */
    public function askAblatazzaAction($page = 1) {
        $em = $this->getDoctrine()->getManager();

        $search = new \stdClass;
        $count = $em->getRepository('CMSBundle:Ask')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 5);
        $asks = $em->getRepository('CMSBundle:Ask')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        return array(
            'asks' => $asks,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/show/{id}", name="fe_ask_show")
     * @Method("GET")
     * @Template()
     */
    public function showAskAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Ask')->find($id);
        return array(
            'ask' => $entity,
        );
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/create-ask", name="fe_ask_create")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request) {
        $entity = new Ask();


        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->get('data');

        $return = TRUE;
        $error = array();
        if (!V::not_null($data['ask'])) {
            array_push($error, "ask");
            $return = FALSE;
        }
        if (!V::not_null($data['email'])) {
            array_push($error, "email");
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

            $referer = $request->headers->get('referer');
            return new \Symfony\Component\HttpFoundation\RedirectResponse($referer);
        }


        $entity->setQuestion($data['ask']);
        $entity->setEmail($data['email']);
        $entity->setPublish(False);

        $em->persist($entity);
        $em->flush();

        $session = new Session();
        $session->getFlashBag()->add('success', 'تم أضافة سؤالك بنجاح وسوف يتم الرد في أسرع وقت');

        $referer = $request->headers->get('referer');
        return new \Symfony\Component\HttpFoundation\RedirectResponse($referer);
    }

}
