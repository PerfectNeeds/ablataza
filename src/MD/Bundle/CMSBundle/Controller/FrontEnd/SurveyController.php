<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MD\Bundle\CMSBundle\Entity\SurveyAnswer;
use MD\Utils\Validate;

/**
 * Recipe controller.
 *
 * @Route("/survey")
 */
class SurveyController extends Controller {

    /**
     * Lists all DynamicPage entities.
     *
     * @Route("/{page}", requirements={"page" = "\d+"}, name="fe_survey")
     * @Method("GET")
     * @Template()
     */
    public function surveyAction($page = 1) {
        $em = $this->getDoctrine()->getManager();
        $pageSeo = $em->getRepository('CMSBundle:DynamicPage')->find(2);

        $search = new \stdClass;
        $search->publish = TRUE;

        $count = $em->getRepository('CMSBundle:Survey')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 9);
        $entities = $em->getRepository('CMSBundle:Survey')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        foreach ($entities as $survey) {

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
            $survey->result = $result;
        }

        return array(
            'page' => $pageSeo,
            'entities' => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/add", name="fe_survey_create_ajax")
     * @Method("POST")
     * @Template()
     */
    public function createSurveyAction(Request $request) {
        $entity = new SurveyAnswer();
        $id = $this->getRequest()->get('id');

        if (!$this->getUser()) {
            $return = array('error' => 1, 'message' => 'Please Login');
            return new \Symfony\Component\HttpFoundation\Response(json_encode($return));
        }

        $em = $this->getDoctrine()->getManager();
        $answer = $this->getRequest()->request->get('answer');

        $return = TRUE;
        $error = array();
        if (!Validate::not_null($answer) AND ! is_numeric($answer) AND $answer > 4) {
            array_push($error, "answer");
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
            $return = array('error' => 1, 'message' => $return);
            return new \Symfony\Component\HttpFoundation\Response(json_encode($return));
        }

        $person = $this->getUser()->getPerson();
        $survey = $em->getRepository('CMSBundle:Survey')->find($id);
        $isSurveyAnswer = $em->getRepository('CMSBundle:SurveyAnswer')->findOneBy(array('survey' => $survey->getId(), 'person' => $person->getId()));
        if ($isSurveyAnswer) {
            $em->remove($isSurveyAnswer);
            $em->flush();
        }

        $entity->setPerson($person);
        $entity->setSurvey($survey);
        $entity->setValue($answer);

        $em->persist($entity);
        $em->flush();

        $surveyRate = $em->getRepository('CMSBundle:Survey')->getSurveyRate($id);

        $totalRate = 0;
        foreach ($surveyRate as $key => $value) {
            $totalRate +=$value;
        }

        $result = array();
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
        $survey->result = $result;
        $return = array('error' => 0,
            'html' => $this->renderView('CMSBundle:FrontEnd\Survey:surveyResultAjax.html.twig', array('entity' => $survey))
        );
        return new \Symfony\Component\HttpFoundation\Response(json_encode($return));
    }

}
