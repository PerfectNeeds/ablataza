<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Utils\Validate;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Form\CaptchaType;
use Symfony\Component\HttpFoundation\Response;

/**
 * contactus controller.
 *
 * @Route("contactus")
 */
class ContactusController extends Controller {

    /**
     * Contactus form.
     *
     * @Route("/", name="fe_contact")
     * @Method("GET")
     * @Template()
     */
    public function contactAction() {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository('CMSBundle:DynamicPage')->find(3);

        return array(
            'contacts' => $contacts,
        );
    }

    /**
     * Lists all Package entities.
     *
     * @Route("/submit", name="fe_contact_submit")
     * @Method("POST")
     * @Template("CMSBundle:FrontEnd\Contactus:thank.html.twig")
     */
    public function thankAction() {


        $name = $this->getRequest()->get('name');
        $email = $this->getRequest()->get('email');
        $msg = $this->getRequest()->get('message');

        $return = TRUE;
        $error = array();

		$reCaptcha = new \MD\Utils\ReCaptcha();
        $reCaptchaValidate = $reCaptcha->verifyResponse(); 
        if ($reCaptchaValidate->success == False) {
			array_push($error, 'Invalid Captcha');
			$return = FALSE;
        }
        if (!Validate::not_null($name)) {
            array_push($error, 'First Name');
            $return = FALSE;
        }
        if (!Validate::not_null($email)) {
            array_push($error, 'Email');
            $return = FALSE;
        }
        if (Validate::not_null($email) AND ! Validate::email($email)) {
            array_push($error, 'Valid Email');
            $return = FALSE;
        }
        if (!Validate::not_null($msg)) {
            array_push($error, 'Message');
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
            return $this->redirect($this->generateUrl('fe_contact'));
        }


        $message = array(
            'subject' => 'Abla Tazza | Contact Thanks',
            'from' => 'info@ablatazza.com',
            'to' => array($email),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Contactus:thankEmail.html.twig', array(
                'name' => $name,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($message);


        // send to Admin
        $messageAdmin = array(
            'subject' => 'Abla Tazza | Contact us from ' . $name,
            'from' => 'no-reply@ablatazza.com',
            'to' => array(\AppKernel::AdminMail),
            'body' => $this->renderView(
                    'CMSBundle:FrontEnd/Contactus:adminEmail.html.twig', array(
                'name' => $name,
                'email' => $email,
                'msg' => $msg,
                    )
            )
        );
        \MD\Utils\Mailer::sendEmail($messageAdmin);

        $session = new Session();
        $session->getFlashBag()->add('success', 'Your message sent successfully');

        return array(
            'name' => $name,
            'email' => $email
        );
    }

}
