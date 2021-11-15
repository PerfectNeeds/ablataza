<?php

namespace MD\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FoodMeasurement
 *
 * @ORM\Table(name="food_measurement")
 * @ORM\Entity()
 */
class FoodMeasurement {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="FoodMeasurementRate", mappedBy="fromMeasurement", cascade={"all"})
     */
    protected $fromMeasurements;

    /**
     * @ORM\OneToMany(targetEntity="FoodMeasurementRate", mappedBy="toMeasurement", cascade={"all"} )
     */
    protected $toMeasurements;

    public function __toString() {
        return $this->getTitle();
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->fromMeasurements = new \Doctrine\Common\Collections\ArrayCollection();
        $this->toMeasurements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return FoodMeasurement
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Add fromMeasurements
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $fromMeasurements
     * @return FoodMeasurement
     */
    public function addFromMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $fromMeasurements) {
        $this->fromMeasurements[] = $fromMeasurements;

        return $this;
    }

    /**
     * Remove fromMeasurements
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $fromMeasurements
     */
    public function removeFromMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $fromMeasurements) {
        $this->fromMeasurements->removeElement($fromMeasurements);
    }

    /**
     * Get fromMeasurements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFromMeasurements() {
        return $this->fromMeasurements;
    }

    /**
     * Add toMeasurements
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $toMeasurements
     * @return FoodMeasurement
     */
    public function addToMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $toMeasurements) {
        $this->toMeasurements[] = $toMeasurements;

        return $this;
    }

    /**
     * Remove toMeasurements
     *
     * @param \MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $toMeasurements
     */
    public function removeToMeasurement(\MD\Bundle\CMSBundle\Entity\FoodMeasurementRate $toMeasurements) {
        $this->toMeasurements->removeElement($toMeasurements);
    }

    /**
     * Get toMeasurements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getToMeasurements() {
        return $this->toMeasurements;
    }

    public function getMeasurementRate($to) {
        foreach ($this->toMeasurements as $toMeasurement) {
            if ($toMeasurement->getFromMeasurement()->getId() == $to) {
                return $toMeasurement->getRate();
            }
        }
        return 0;
    }

}
