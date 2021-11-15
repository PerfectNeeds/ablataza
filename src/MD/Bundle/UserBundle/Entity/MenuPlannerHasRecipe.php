<?php

namespace MD\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuPlannerHasRecipes
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="menu_planner_has_recipe")
 * @ORM\Entity()
 */
class MenuPlannerHasRecipe {

    /**
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity="MenuPlanner", inversedBy="menuPlannerHasRecipes")
     */
    protected $menuPlanner;

    /**
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\Recipe", inversedBy="menuPlannerHasRecipes")
     */
    protected $recipe;

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
     * Set created
     *
     * @param \DateTime $created
     * @return MenuPlannerHasRecipe
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
     * Set menuPlanner
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanner
     * @return MenuPlannerHasRecipe
     */
    public function setMenuPlanner(\MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanner) {
        $this->menuPlanner = $menuPlanner;

        return $this;
    }

    /**
     * Get menuPlanner
     *
     * @return \MD\Bundle\UserBundle\Entity\MenuPlanner 
     */
    public function getMenuPlanner() {
        return $this->menuPlanner;
    }

    /**
     * Set recipe
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipe
     * @return MenuPlannerHasRecipe
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

}
