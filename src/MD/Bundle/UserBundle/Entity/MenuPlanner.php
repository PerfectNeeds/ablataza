<?php

namespace MD\Bundle\UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenuPlanner
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("menu_planner")
 * @ORM\Entity(repositoryClass="MD\Bundle\UserBundle\Repository\MenuPlannerRepository")
 */
class MenuPlanner {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\Seo", inversedBy="menuPlanner", cascade={"remove"} )
     */
    protected $seo;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="menuPlanners")
     */
    protected $person;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="MenuPlannerHasRecipe", mappedBy="menuPlanner", cascade={"all"})
     */
    protected $menuPlannerHasRecipes;

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
        $this->menuPlannerHasRecipes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MenuPlanner
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
     * @return MenuPlanner
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
     * Set created
     *
     * @param \DateTime $created
     * @return MenuPlanner
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
     * Set seo
     *
     * @param \MD\Bundle\CMSBundle\Entity\Seo $seo
     * @return MenuPlanner
     */
    public function setSeo(\MD\Bundle\CMSBundle\Entity\Seo $seo = null) {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \MD\Bundle\CMSBundle\Entity\Seo 
     */
    public function getSeo() {
        return $this->seo;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return MenuPlanner
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
     * Add menuPlannerHasRecipes
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe $menuPlannerHasRecipes
     * @return MenuPlanner
     */
    public function addMenuPlannerHasRecipe(\MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe $menuPlannerHasRecipes) {
        $this->menuPlannerHasRecipes[] = $menuPlannerHasRecipes;

        return $this;
    }

    /**
     * Remove menuPlannerHasRecipes
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe $menuPlannerHasRecipes
     */
    public function removeMenuPlannerHasRecipe(\MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe $menuPlannerHasRecipes) {
        $this->menuPlannerHasRecipes->removeElement($menuPlannerHasRecipes);
    }

    /**
     * Get menuPlannerHasRecipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuPlannerHasRecipes() {
        return $this->menuPlannerHasRecipes;
    }

    public function getFirstRecipe() {
        return $this->getMenuPlannerHasRecipes()->first()->getRecipe();
    }

    /**
     * is menuPlanner has recipe id
     * @param type $recipeId
     * @return boolean
     */
    public function isMenuPlannerHasRecipe($recipeId) {
        foreach ($this->menuPlannerHasRecipes as $menuPlannerHasRecipe) {
            if ($menuPlannerHasRecipe->getRecipe()->getId() == $recipeId) {
                return TRUE;
            }
        }
        return FALSE;
    }

}
