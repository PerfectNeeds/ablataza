<?php

namespace MD\Bundle\CMSBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Utils\Validate as V;

/**
 * Fadfada controller.
 *
 * @Route("/calculator")
 */
class CalculatorController extends Controller {

    /**
     * Lists all Supplier entities.
     *
     * @Route("/bmi", name="fe_calculator")
     * @Method("GET")
     * @Template()
     */
    public function BMIAction() {
        $gender = $this->getRequest()->get('g');
        $height = $this->getRequest()->get('h');
        $weight = $this->getRequest()->get('w');
        $bmi = NULL;
        $bestWeight = NULL;
        if (V::not_null($gender) AND V::not_null($height) AND V::not_null($weight)) {
            $bmi = ($weight / pow($height / 100, 2));
            $bestWeight = pow($height / 100, 2) * 25;
        }

        return array(
            "gender" => $gender,
            "height" => $height,
            "weight" => $weight,
            "bmi" => $bmi,
            "bestWeight" => $bestWeight,
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/food-measurment", name="fe_food_measurment")
     * @Method("GET")
     * @Template()
     */
    public function foodMeasurmentAction() {
        $em = $this->getDoctrine()->getManager();
        $foodMeasurements = $em->getRepository('CMSBundle:FoodMeasurement')->findAll();

        $value = $this->getRequest()->get('v');
        $fromMeasurement = $this->getRequest()->get('f');
        $toMeasurement = $this->getRequest()->get('t');
        $rate = NULL;
        if (V::not_null($value) AND is_numeric($value)) {
            $toMeasurement = $em->getRepository('CMSBundle:FoodMeasurement')->find($toMeasurement);
            $fromMeasurement = $em->getRepository('CMSBundle:FoodMeasurement')->find($fromMeasurement);
            $rate = $value * $toMeasurement->getMeasurementRate($fromMeasurement->getId());
        }

        return array(
            'foodMeasurements' => $foodMeasurements,
            'fromMeasurement' => $fromMeasurement,
            'toMeasurement' => $toMeasurement,
            'value' => $value,
            'rate' => $rate,
        );
    }

    /**
     * Lists all Supplier entities.
     *
     * @Route("/due-date", name="fe_due_date")
     * @Method("GET")
     * @Template()
     */
    public function dueDateAction() {
        $date = $this->getRequest()->get('date');
        $dueDate = NULL;
        if (V::not_null($date)) {
            $dueDate = date("d-m-Y", strtotime("+280 day", strtotime($date)));
        }
        return array(
            "date" => $date,
            "dueDate" => $dueDate,
        );
    }

}
