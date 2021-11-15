<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("fadfada")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\FadfadaRepository")
 */
class Fadfada {

    CONST MARITAL_STATUS_MARRIED = 1; //متزوجة
    CONST MARITAL_STATUS_ENGAGED = 2; //مخطوبه
    CONST MARITAL_STATUS_FRIENDSHIP = 3; //مصاحبه
    CONST MARITAL_STATUS_SINGEL = 4; //Singel
    CONST MARITAL_STATUS_OTHER = 5; //Other

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
     * @ORM\Column(name="text", type="text")
     */
    protected $text;

    /**
     * @var string
     *
     * @ORM\Column(name="marital_status", type="smallint")
     */
    protected $maritalStatus;

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
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    protected $views;

    /**
     * @ORM\OneToMany(targetEntity="FadfadaComment", mappedBy="fadfada", cascade={"all"})
     */
    protected $fadfadaComments;

    /**
     * @ORM\OneToOne(targetEntity="FadfadaFavorite", mappedBy="fadfada", cascade={"all"})
     */
    protected $fadfadaFavorite;

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
     * Set text
     *
     * @param string $text
     * @return Fadfada
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     * @return Fadfada
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
     * Constructor
     */
    public function __construct() {
        $this->fadfadaComments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add fadfadaComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\FadfadaComment $fadfadaComments
     * @return Fadfada
     */
    public function addFadfadaComment(\MD\Bundle\CMSBundle\Entity\FadfadaComment $fadfadaComments) {
        $this->fadfadaComments[] = $fadfadaComments;

        return $this;
    }

    /**
     * Remove fadfadaComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\FadfadaComment $fadfadaComments
     */
    public function removeFadfadaComment(\MD\Bundle\CMSBundle\Entity\FadfadaComment $fadfadaComments) {
        $this->fadfadaComments->removeElement($fadfadaComments);
    }

    /**
     * Get fadfadaComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFadfadaComments() {
        return $this->fadfadaComments;
    }

    /**
     * Set maritalStatus
     *
     * @param integer $maritalStatus
     * @return Fadfada
     */
    public function setMaritalStatus($maritalStatus) {
        $this->maritalStatus = $maritalStatus;

        return $this;
    }

    /**
     * Get maritalStatus
     *
     * @return integer 
     */
    public function getMaritalStatus() {
        return $this->maritalStatus;
    }

    /**
     * Set fadfadaFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\FadfadaFavorite $fadfadaFavorite
     * @return Fadfada
     */
    public function setFadfadaFavorite(\MD\Bundle\CMSBundle\Entity\FadfadaFavorite $fadfadaFavorite = null) {
        $this->fadfadaFavorite = $fadfadaFavorite;

        return $this;
    }

    /**
     * Get fadfadaFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\FadfadaFavorite 
     */
    public function getFadfadaFavorite() {
        return $this->fadfadaFavorite;
    }

    /**
     * Set views
     *
     * @param \DateTime $views
     * @return Recipe
     */
    public function setViews($views) {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return \DateTime 
     */
    public function getViews() {
        return $this->views;
    }

    /**
     * Get checkFadfadaFavByUserId
     *
     * @return Bool
     */
    public function isFadfadaFavByUserId($userId) {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository('CMSBundle:Fadfada')->checkFadfadaFavByUserId($this->getId(), $userId);
    }

}
