<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Utils\Validate;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Form\CaptchaType;

/**
 * career controller.
 *
 * @Route("career")
 */
class CareerController extends Controller {

    /**
     * Career form.
     *
     * @Route("/", name="fe_career")
     * @Method("GET")
     * @Template()
     */
    public function careerAction() {
        $em = $this->getDoctrine()->getManager();
        $careers = $em->getRepository('CMSBundle:DynamicPage')->find(13);

        return array(
            'careers' => $careers,
        );
    }

    /**
     * Lists all Package entities.
     *
     * @Route("/submit", name="fe_career_submit")
     * @Method("POST")
     * @Template("CMSBundle:FrontEnd\Career:career.html.twig")
     */
    public function thankAction() {
        $return = TRUE;
        $error = array();
        $attachment = FALSE;

        $mimeTypes = array(
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        );

        $firstName = $this->getRequest()->get('fname');
        $lastName = $this->getRequest()->get('lname');
        $address = $this->getRequest()->get('address');
        $city = $this->getRequest()->get('city');
        $primaryNumber = $this->getRequest()->get('pnumber');
        $primaryNumberType = $this->getRequest()->get('pnumbertype');
        $secondaryNumber = $this->getRequest()->get('snumber');
        $secondaryNumberType = $this->getRequest()->get('snumbertype');
        $email = $this->getRequest()->get('email');
        $job = $this->getRequest()->get('job');


        if (!Validate::not_null($firstName)) {
            array_push($error, 'Fisrt Name');
            $return = FALSE;
        }
        if (!Validate::not_null($lastName)) {
            array_push($error, 'Last Name');
            $return = FALSE;
        }
        if (!Validate::not_null($address)) {
            array_push($error, 'Address');
            $return = FALSE;
        }
        if (!Validate::not_null($city)) {
            array_push($error, 'City');
            $return = FALSE;
        }
        if (!Validate::not_null($primaryNumber)) {
            array_push($error, 'Primary Contact Number');
            $return = FALSE;
        }
        if (!Validate::not_null($secondaryNumber)) {
            array_push($error, 'Secondary Contact Number');
            $return = FALSE;
        }
        if (!Validate::not_null($job)) {
            array_push($error, 'Regarding Job');
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

        if (isset($_FILES['file']['tmp_name']) and ! empty($_FILES['file']['tmp_name'])) {
            $attachment = TRUE;
            $file = $_FILES['file']['tmp_name'];

            $fileMimeType = mime_content_type($file);
            if (!in_array($fileMimeType, $mimeTypes)) {
                array_push($error, 'Attachment Allowed extensions');
                $return = FALSE;
            }
        } else {
            array_push($error, 'Attachment');
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

            $em = $this->getDoctrine()->getManager();
            $careers = $em->getRepository('CMSBundle:DynamicPage')->find(13);

            return array(
                'careers' => $careers,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'address' => $address,
                'city' => $city,
                'primaryNumber' => $primaryNumber,
                'primaryNumberType' => $primaryNumberType,
                'secondaryNumber' => $secondaryNumber,
                'secondaryNumberType' => $secondaryNumberType,
                'email' => $email,
                'job' => $job,
            );
        }


        $message = \Swift_Message::newInstance()
                ->setSubject('Farm Frites Egypt | Thanks to submitted a career form')
                ->setFrom('marketing@farmfrites.com.eg')
                ->setTo($email)
                ->setBody(
                $this->renderView(
                        'CMSBundle:FrontEnd/Career:thankEmail.html.twig', array(
                    'name' => $firstName . ' ' . $lastName
                        )
                )
                , 'text/html');
        $this->get('mailer')->send($message);
        // send to Admin
        $messageAdmin = \Swift_Message::newInstance()
                ->setSubject('Farm Frites Egypt | ' . $firstName . ' ' . $lastName . ' has submitted a career form')
                ->setFrom($email, $firstName . ' ' . $lastName)
                ->setTo('marketing@farmfrites.com.eg')
                ->setBody(
                $this->renderView(
                        'CMSBundle:FrontEnd/Career:adminEmail.html.twig', array(
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'address' => $address,
                    'city' => $city,
                    'primaryNumber' => $primaryNumber,
                    'primaryNumberType' => $primaryNumberType,
                    'secondaryNumber' => $secondaryNumber,
                    'secondaryNumberType' => $secondaryNumberType,
                    'email' => $email,
                    'job' => $job,
                        )
                )
                , 'text/html');
        if ($attachment) {
            $messageAdmin->attach(\Swift_Attachment::fromPath($file, $fileMimeType));
        }
        $this->get('mailer')->send($messageAdmin);

        $session = new Session();
        $session->getFlashBag()->add('success', 'Your message sent successfully');

        return $this->redirect($this->generateUrl('fe_career'));
    }

}
