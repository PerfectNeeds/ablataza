<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("recipe_comment")
 * @ORM\Entity()
 */
class RecipeComment {

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Recipe", inversedBy="recipeComments")
     */
    protected $recipe;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=150)
     */
    protected $comment;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="rating", type="float", nullable=true)
     */
    protected $rating = null;

    /**
     *
     * @ORM\Column(name="publish", type="boolean")
     */
    protected $publish = false;

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
     * Set name
     *
     * @param string $name
     * @return BloggerComment
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return BloggerComment
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

    /**
     * Set comment
     *
     * @param string $comment
     * @return BloggerComment
     */
    public function setComment($comment) {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return Rating
     */
    public function setRating($rating) {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float 
     */
    public function getRating() {
        return $this->rating;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PackageComment
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
     * Set publish
     *
     * @param boolean $publish
     * @return Recipe
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

    /**
     * Set recipe
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipe
     * @return RecipeComment
     */
    public function setRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipe = null) {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * Get recipe
     *
     * @return \MD\Bundle\CMSBundle\Entity\Recipe 
     */
    public function getRecipe() {
        return $this->recipe;
    }

}
