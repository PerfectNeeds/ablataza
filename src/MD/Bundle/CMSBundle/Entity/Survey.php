<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("survey")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\SurveyRepository")
 */
class Survey {

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
     * @ORM\Column(name="question", type="text")
     */
    protected $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer1", type="text")
     */
    protected $answer1;

    /**
     * @var string
     *
     * @ORM\Column(name="answer2", type="text")
     */
    protected $answer2;

    /**
     * @var string
     *
     * @ORM\Column(name="answer3", type="text")
     */
    protected $answer3;

    /**
     * @var string
     *
     * @ORM\Column(name="answer4", type="text")
     */
    protected $answer4;

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="survey", cascade={"all"})
     */
    protected $surveyAnswers;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\MediaBundle\Entity\Image", inversedBy="survey")
     */
    protected $image;

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
     * Constructor
     */
    public function __construct() {
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set question
     *
     * @param string $question
     * @return Survey
     */
    public function setQuestion($question) {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string 
     */
    public function getQuestion() {
        return $this->question;
    }

    /**
     * Set answer1
     *
     * @param string $answer1
     * @return Survey
     */
    public function setAnswer1($answer1) {
        $this->answer1 = $answer1;

        return $this;
    }

    /**
     * Get answer1
     *
     * @return string 
     */
    public function getAnswer1() {
        return $this->answer1;
    }

    /**
     * Set answer2
     *
     * @param string $answer2
     * @return Survey
     */
    public function setAnswer2($answer2) {
        $this->answer2 = $answer2;

        return $this;
    }

    /**
     * Get answer2
     *
     * @return string 
     */
    public function getAnswer2() {
        return $this->answer2;
    }

    /**
     * Set answer3
     *
     * @param string $answer3
     * @return Survey
     */
    public function setAnswer3($answer3) {
        $this->answer3 = $answer3;

        return $this;
    }

    /**
     * Get answer3
     *
     * @return string 
     */
    public function getAnswer3() {
        return $this->answer3;
    }

    /**
     * Set answer4
     *
     * @param string $answer4
     * @return Survey
     */
    public function setAnswer4($answer4) {
        $this->answer4 = $answer4;

        return $this;
    }

    /**
     * Get answer4
     *
     * @return string 
     */
    public function getAnswer4() {
        return $this->answer4;
    }

    /**
     * Add surveyAnswers
     *
     * @param \MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers
     * @return Survey
     */
    public function addSurveyAnswer(\MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers) {
        $this->surveyAnswers[] = $surveyAnswers;

        return $this;
    }

    /**
     * Remove surveyAnswers
     *
     * @param \MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers
     */
    public function removeSurveyAnswer(\MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers) {
        $this->surveyAnswers->removeElement($surveyAnswers);
    }

    /**
     * Get surveyAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSurveyAnswers() {
        return $this->surveyAnswers;
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

    public function isePersonAnswer($userId) {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository('CMSBundle:Survey')->checkPersonHasAnswer($this->getId(), $userId);
    }


    /**
     * Set image
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $image
     * @return Survey
     */
    public function setImage(\MD\Bundle\MediaBundle\Entity\Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \MD\Bundle\MediaBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }
}