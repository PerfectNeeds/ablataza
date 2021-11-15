<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("tip")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\TipRepository")
 */
class Tip {

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
     * @ORM\Column(name="tip", type="text")
     */
    protected $tip;

    /**
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="tips")
     */
    protected $person;

    /**
     * @ORM\ManyToOne(targetEntity="SuperCategory", inversedBy="tips")
     */
    protected $superCategory;

    /**
     *
     * @ORM\Column(name="publish", type="boolean")
     */
    protected $publish = true;

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
     * Set tip
     *
     * @param string $tip
     * @return Tip
     */
    public function setTip($tip) {
        $this->tip = $tip;

        return $this;
    }

    /**
     * Get tip
     *
     * @return string 
     */
    public function getTip() {
        return $this->tip;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return Tip
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
     * Set publish
     *
     * @param boolean $publish
     * @return Article
     */
    public function setPublish($publish) {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return boolean 
     */
    public function getPublish() {
        return $this->publish;
    }

    /** Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Blogger
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }


    /**
     * Set superCategory
     *
     * @param \MD\Bundle\CMSBundle\Entity\SuperCategory $superCategory
     * @return Tip
     */
    public function setSuperCategory(\MD\Bundle\CMSBundle\Entity\SuperCategory $superCategory = null)
    {
        $this->superCategory = $superCategory;
    
        return $this;
    }

    /**
     * Get superCategory
     *
     * @return \MD\Bundle\CMSBundle\Entity\SuperCategory 
     */
    public function getSuperCategory()
    {
        return $this->superCategory;
    }
}