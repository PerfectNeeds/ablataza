<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("survey_answer")
 * @ORM\Entity()
 */
class SurveyAnswer {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="surveyAnswers")
     */
    protected $survey;

    /**
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="surveyAnswers")
     */
    protected $person;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="smallint")
     */
    protected $value;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * Now we tell doctrine that before we persist or update we call the updatedTimestamps() function.
     *
     * @ORM\PrePersist
     */
    public function

    updatedTimestamps() {
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
     * Set value
     *
     * @param integer $value
     * @return SurveyAnswer
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set survey
     *
     * @param \MD\Bundle\CMSBundle\Entity\Survey $survey
     * @return SurveyAnswer
     */
    public function setSurvey(\MD\Bundle\CMSBundle\Entity\Survey $survey = null) {
        $this->survey = $survey;


        return $this;
    }

    /**
     * Get survey
     *
     * @return \MD\Bundle\CMSBundle\Entity\Survey 
     */
    public function getSurvey() {
        return $this->survey;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return SurveyAnswer
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

}
