<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * FoodMeasurement
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="food_measurement_rate")
 * @ORM\Entity()
 */
class FoodMeasurementRate {

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="FoodMeasurement", inversedBy="fromMeasurements")
     */
    protected $fromMeasurement;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="FoodMeasurement", inversedBy="toMeasurements")
     */
    protected $toMeasurement;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float")
     */
    protected $rate;

    /**
     * Set rate
     *
     * @param float $rate
     * @return FoodMeasurementRate
     */
    public function setRate($rate) {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * Set fromMeasurement
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurement $fromMeasurement
     * @return FoodMeasurementRate
     */
    public function setFromMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurement $fromMeasurement) {
        $this->fromMeasurement = $fromMeasurement;

        return $this;
    }

    /**
     * Get fromMeasurement
     *
     * @return \MD\Bundle\CMSBundle\Entity\FoodMeasurement 
     */
    public function getFromMeasurement() {
        return $this->fromMeasurement;
    }

    /**
     * Set toMeasurement
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurement $toMeasurement
     * @return FoodMeasurementRate
     */
    public function setToMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurement $toMeasurement) {
        $this->toMeasurement = $toMeasurement;

        return $this;
    }

    /**
     * Get toMeasurement
     *
     * @return \MD\Bundle\CMSBundle\Entity\FoodMeasurement 
     */
    public function getToMeasurement() {
        return $this->toMeasurement;
    }

}
