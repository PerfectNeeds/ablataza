<?php

namespace MD\Bundle\CMSBundle\Controller\Administration;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MD\Bundle\CMSBundle\Entity\FoodMeasurement;
use MD\Bundle\CMSBundle\Entity\FoodMeasurementRate;
use MD\Bundle\CMSBundle\Form\FoodMeasurementType;
use MD\Utils\Validate;

/**
 * FoodMeasurement controller.
 *
 * @Route("/food-measurement")
 */
class FoodMeasurementController extends Controller {

    /**
     * Lists all FoodMeasurement entities.
     *
     * @Route("/", name="foodmeasurement")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CMSBundle:FoodMeasurement')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new FoodMeasurement entity.
     *
     * @Route("/", name="foodmeasurement_create")
     * @Method("POST")
     * @Template("CMSBundle:Administration/FoodMeasurement:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new FoodMeasurement();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('foodmeasurement'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a FoodMeasurement entity.
     *
     * @param FoodMeasurement $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FoodMeasurement $entity) {
        $form = $this->createForm(new FoodMeasurementType(), $entity, array(
            'action' => $this->generateUrl('foodmeasurement_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new FoodMeasurement entity.
     *
     * @Route("/new", name="foodmeasurement_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new FoodMeasurement();
        $form = $this->createCreateForm($entity);


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FoodMeasurement entity.
     *
     * @Route("/{id}/edit", name="foodmeasurement_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:FoodMeasurement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FoodMeasurement entity.');
        }

        $editForm = $this->createEditForm($entity);


        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a FoodMeasurement entity.
     *
     * @param FoodMeasurement $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(FoodMeasurement $entity) {
        $form = $this->createForm(new FoodMeasurementType(), $entity, array(
            'action' => $this->generateUrl('foodmeasurement_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Edits an existing FoodMeasurement entity.
     *
     * @Route("/{id}", name="foodmeasurement_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration/FoodMeasurement:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:FoodMeasurement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FoodMeasurement entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('foodmeasurement_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete", name="foodmeasurement_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request) {
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:FoodMeasurement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FoodMeasurement entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('foodmeasurement'));
    }

    /**
     * Lists all FoodMeasurement entities.
     *
     * @Route("/rate", name="foodmeasurement_rate")
     * @Method("GET")
     * @Template()
     */
    public function foodMeasurementRateAction() {
        $em = $this->getDoctrine()->getManager();

        $foodMeasurements = $em->getRepository('CMSBundle:FoodMeasurement')->findAll();

        return array(
            'foodMeasurements' => $foodMeasurements,
        );
    }

    /**
     * Lists all FoodMeasurement entities.
     *
     * @Route("/update/rate", name="foodmeasurement_rate_update")
     * @Method("PUT")
     * @Template("CMSBundle:Administration\FoodMeasurement:foodMeasurementRate.html.twig")
     */
    public function updateFoodMeasurementRateAction() {
        $em = $this->getDoctrine()->getManager();

        $foodMeasurements = $em->getRepository('CMSBundle:FoodMeasurement')->findAll();

        $rate = $this->getRequest()->request->get('rate');

        foreach ($rate as $fromKey => $fromValue) {
            foreach ($fromValue as $toKey => $toValue) {
                $foodMeasurementRate = $em->getRepository('CMSBundle:FoodMeasurementRate')->findOneBy(array('fromMeasurement' => $fromKey, 'toMeasurement' => $toKey));
                echo "$fromKey -> $toKey = $toValue<br>";
                if (!$foodMeasurementRate) {
                    $foodMeasurementRate = new FoodMeasurementRate();
                    $fromCurr = $em->getRepository('CMSBundle:FoodMeasurement')->find($fromKey);
                    $foodMeasurementRate->setFromMeasurement($fromCurr);
                    $toCurr = $em->getRepository('CMSBundle:FoodMeasurement')->find($toKey);
                    $foodMeasurementRate->setToMeasurement($toCurr);
                }

                $toValue = (Validate::not_null($toValue)) ? $toValue : 0;
                $foodMeasurementRate->setRate($toValue);
                $em->persist($foodMeasurementRate);
            }
        }

        $em->flush();
        return $this->redirect($this->generateUrl('foodmeasurement_rate'));
    }

}
