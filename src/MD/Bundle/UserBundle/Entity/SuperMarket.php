<?php

namespace MD\Bundle\UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * SuperMarket
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("super_market")
 * @ORM\Entity()
 */
class SuperMarket {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="superMarkets")
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\IngredientCategory", inversedBy="superMarkets")
     */
    protected $ingredientCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=250)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="checked", type="boolean")
     */
    protected $checked = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     */
    public function updatedTimestamps() {
        $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }
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
     * @return SuperMarket
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
     * Set description
     *
     * @param string $description
     * @return SuperMarket
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     * @return SuperMarket
     */
    public function setChecked($checked) {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return boolean 
     */
    public function getChecked() {
        return $this->checked;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return SuperMarket
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return SuperMarket
     */
    public function setPerson(\MD\Bundle\UserBundle\Entity\Person $person = null) {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \MD\Bundle\UserBundle\Entity\Person 
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * Set ingredientCategory
     *
     * @param \MD\Bundle\CMSBundle\Entity\IngredientCategory $ingredientCategory
     * @return SuperMarket
     */
    public function setIngredientCategory(\MD\Bundle\CMSBundle\Entity\IngredientCategory $ingredientCategory = null) {
        $this->ingredientCategory = $ingredientCategory;

        return $this;
    }

    /**
     * Get ingredientCategory
     *
     * @return \MD\Bundle\CMSBundle\Entity\IngredientCategory 
     */
    public function getIngredientCategory() {
        return $this->ingredientCategory;
    }

}
