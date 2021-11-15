<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("ask")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\AskRepository")
 */
class Ask {

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
     * @ORM\Column(name="email", type="string", length=150)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text")
     */
    protected $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    protected $answer = NULL;

    /**
     * @var string
     *
     * @ORM\Column(name="publish", type="boolean")
     */
    protected $publish = FALSE;

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
     * Set question
     *
     * @param string $question
     * @return Question
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
     * Set answer
     *
     * @param string $answer
     * @return Ask
     */
    public function setAnswer($answer) {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer() {
        return $this->answer;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     * @return Ask
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
     * Set email
     *
     * @param string $email
     * @return Ask
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

}
