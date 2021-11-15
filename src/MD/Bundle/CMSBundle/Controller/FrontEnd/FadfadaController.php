<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\Fadfada;
use MD\Bundle\CMSBundle\Entity\FadfadaFavorite;
use MD\Utils\Validate as V;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Fadfada controller.
 *
 * @Route("/fadfada")
 */
class FadfadaController extends Controller {

    /**
     * Lists all Supplier entities.
     *
     * @Route("/{page}", requirements={"page" = "\d+"},  name="fe_fadfada")
     * @Method("GET")
     * @Template()
     */
    public function fadfadaAction($page = 1) {
        $em = $this->getDoctrine()->getManager();
        $collectPost = new \stdClass;
        $count = $em->getRepository('CMSBundle:Fadfada')->filter($collectPost, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page);
        $entities = $em->getRepository('CMSBundle:Fadfada')->filter($collectPost, FALSE, NULL, $paginator->getLimitStart(), $paginator->getPageLimit());

        $videos = $em->getRepository('CMSBundle:FadfadaVideo')->findBy(array('publish' => TRUE), array('id' => 'DESC'), 10);
        return array(
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
            'videos' => $videos,
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/show/{id}", name="fe_fadfada_show")
     * @Method("GET")
     * @Template()
     */
    public function showFadfadaAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Fadfada')->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $comments = $em->getRepository('CMSBundle:FadfadaComment')->findBy(array('fadfada' => $entity->getId(), 'publish' => TRUE));

        $session = $this->getRequest()->getSession();
        if (!$session->get('FadfadaViews') || !in_array($entity->getId(), $session->get('FadfadaViews'))) {
            $temp = (array) $session->get('FadfadaViews');
            array_push($temp, $entity->getId());
            $session->set('FadfadaViews', $temp);
            $noViews = ($entity->getViews() == null) ? 0 : $entity->getViews();
            $noViews++;
            $entity->setViews($noViews);
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
        }

        return array(
            'entity' => $entity,
            'comments' => $comments,
        );
    }

    /**
     * Creates a new Fadfada entity.
     *
     * @Route("/create-fadfada", name="fe_fadfada_create")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request) {
        $entity = new Fadfada();


        $em = $this->getDoctrine()->getManager();
        $data = $this->getRequest()->request->get('data');

        $return = TRUE;
        $error = array();
		
		$reCaptcha = new \MD\Utils\ReCaptcha();
        $reCaptchaValidate = $reCaptcha->verifyResponse(); 
        if ($reCaptchaValidate->success == False) {
			array_push($error, 'Invalid Captcha');
			$return = FALSE;
        }
		
        if (!V::not_null($data['text'])) {
            array_push($error, "text");
            $return = FALSE;
        }

        if (!V::not_null($data['maritalStatus'])) {
            array_push($error, "maritalStatus");
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


        $entity->setText($data['text']);
        $entity->setMaritalStatus($data['maritalStatus']);
        $entity->setPublish(False);

        $em->persist($entity);
        $em->flush();
        $em->refresh($entity);

        $message = array(
            'subject' => 'Abla Tazza added a new Fadfada',
            'from' => 'info@ablatazza.com',
            'to' => array('nourhan.shweter@4saleegypt.com ', 'jailan.elbayaa@4saleegypt.com', 'hams.alaa@4saleegypt.com', 'ablatazza@gmail.com'),
//            'to' => array('peter.nassef@gmail.com'),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Fadfada:addEmail.html.twig', array(
                'entity' => $entity,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        $session = new Session();
        $session->getFlashBag()->add('success', 'تم إضافة فضفتك بنجاح وسوف يتم الرد في أسرع وقت');

        $referer = $request->headers->get('referer');
        return new \Symfony\Component\HttpFoundation\RedirectResponse($referer);
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/favorite/ajax", name="fe_fadfada_favorite_ajax")
     * @Method("POST")
     */
    public function favoriteAction() {
        $id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $checkFavorite = $em->getRepository('CMSBundle:FadfadaFavorite')->findOneBy(array('fadfada' => $id, 'person' => $this->getUser()->getId()));
        if (!$checkFavorite) {
            $fadfadaFavorite = new FadfadaFavorite();
            $fadfadaFavorite->setPerson($this->getUser()->getPerson());
            $fadfada = $em->getRepository('CMSBundle:Fadfada')->find($id);
            $fadfadaFavorite->setFadfada($fadfada);
            $em->persist($fadfadaFavorite);
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

		$reCaptcha = new \MD\Utils\ReCaptcha();
        $reCaptchaValidate = $reCaptcha->verifyResponse(); 
        if ($reCaptchaValidate->success == False) {
			array_push($errors, 'Invalid Captcha');
        }
		
        if (!V::not_null($comment->name)) {
            $message = $this->get('translator')->trans("You must enter your name");
            array_push($errors, $message);
        }

        if (!V::not_null($comment->comment)) {
            $message = "You must enter you comment";
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
     * @Route("/comment/{id}", name="fe_fadfadacomment_create")
     * @Method("POST")
     * @Template("CMSBundle:FrontEnd/showFadfada:new.html.twig")
     */
    public function createCommentAction($id) {
        $entity = new \MD\Bundle\CMSBundle\Entity\FadfadaComment();

        $collectPost = $this->collectCommentPOST();
        $validate = $this->validateComment($collectPost);
        if ($validate !== TRUE) {
            $session = new \Symfony\Component\HttpFoundation\Session\Session();
            $session->getFlashBag()->add('commentError', $validate);
            return $this->redirect($this->generateUrl('fe_fadfada', array('id' => $id)) . '#comment');
        }

        $em = $this->getDoctrine()->getManager();

        $fadfada = $em->getRepository('CMSBundle:Fadfada')->find($id);
        $entity->setFadfada($fadfada);
        $entity->setName($collectPost->name);
        $entity->setComment($collectPost->comment);

        $em->persist($entity);
        $em->flush();

        $session = new \Symfony\Component\HttpFoundation\Session\Session();
        $session->getFlashBag()->add('success', $validate);

        $message = array(
            'subject' => 'Abla Tazza Fadfada Comment',
            'from' => 'info@ablatazza.com',
            'to' => array('nourhan.shweter@4saleegypt.com ', 'jailan.elbayaa@4saleegypt.com', 'hams.alaa@4saleegypt.com', 'ablatazza@gmail.com'),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Fadfada:commentEmail.html.twig', array(
                'parm' => $collectPost,
                'entity' => $entity,
                'fadfada' => $fadfada,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);

        return $this->redirect($this->generateUrl('fe_fadfada_show', array('id' => $id)));
    }

}
