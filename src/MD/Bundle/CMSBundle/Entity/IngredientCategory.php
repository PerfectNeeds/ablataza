<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * IngredientCategory
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("ingredient_category")
 * @ORM\Entity()
 */
class IngredientCategory {

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
     * @ORM\OneToOne(targetEntity="\MD\Bundle\MediaBundle\Entity\Image", inversedBy="ingredientCategory")
     */
    protected $image;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\UserBundle\Entity\SuperMarket", mappedBy="ingredientCategory")
     */
    protected $superMarkets;

    /**
     * Constructor
     */
    public function __construct() {
        $this->superMarkets = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return IngredientCategory
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
     * Set image
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $image
     * @return HotelFacility
     */
    public function setImage(\MD\Bundle\MediaBundle\Entity\Image $image = null) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \MD\Bundle\MediaBundle\Entity\Image 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return IngredientCategory
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Add superMarkets
     *
     * @param \MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets
     * @return IngredientCategory
     */
    public function addSuperMarket(\MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets) {
        $this->superMarkets[] = $superMarkets;

        return $this;
    }

    /**
     * Remove superMarkets
     *
     * @param \MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets
     */
    public function removeSuperMarket(\MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets) {
        $this->superMarkets->removeElement($superMarkets);
    }

    /**
     * Get superMarkets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSuperMarkets() {
        return $this->superMarkets;
    }

}
