<?php

namespace MD\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecipeFavorite
 *
 * @ORM\Table(name="recipe_favorite")
 * @ORM\Entity
 */
class RecipeFavorite {

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="Recipe", inversedBy="recipeFavorite")
     * @ORM\JoinColumn(name="recipe_id" ,referencedColumnName="id")
     */
    protected $recipe;

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="recipeFavorite")
     * @ORM\JoinColumn(name="person_id" ,referencedColumnName="id")
     */
    protected $person;

    /**
     * Set recipe
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipe
     * @return RecipeFavorite
     */
    public function setRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipe) {
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

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return RecipeFavorite
     */
    public function setPerson(\MD\Bundle\UserBundle\Entity\Person $person) {
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

}
