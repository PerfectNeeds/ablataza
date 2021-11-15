<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("recipe")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\RecipeRepository")
 */
class Recipe {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * ingredients
     * description
     * @ORM\OneToOne(targetEntity="Post", inversedBy="recipe")
     */
    protected $post;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="recipe")
     */
    protected $seo;

    /**
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="recipes")
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
     * @ORM\Column(name="youtube_url", type="string", length=255,  nullable=true)
     */
    protected $youtubeUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="people_no", type="integer", nullable=true)
     */
    protected $peopleNo;

    /**
     * @ORM\Column(name="preparation_time", type="integer", nullable=true)
     */
    protected $preparationTime;

    /**
     * @ORM\Column(name="cooking_time", type="integer", nullable=true)
     */
    protected $cookingTime;

    /**
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    protected $views;

    /**
     *
     * @ORM\Column(name="publish", type="boolean")
     */
    protected $publish = true;

    /**
     *
     * @ORM\Column(name="draft", type="boolean")
     */
    protected $draft = true;

    /**
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    protected $deleted = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\ManyToMany(targetEntity="SubCategory", inversedBy="recipes")
     */
    protected $subCategories;

    /**
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="recipes")
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="RecipeComment", mappedBy="recipe", cascade={"all"})
     */
    protected $recipeComments;

    /**
     * @ORM\OneToOne(targetEntity="RecipeFavorite", mappedBy="recipe", cascade={"all"})
     */
    protected $recipeFavorite;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe", mappedBy="recipe", cascade={"all"})
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
        $this->subCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Recipe
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
     * Set youtubeUrl
     *
     * @param string $youtubeUrl
     * @return Recipe
     */
    public function setYoutubeUrl($youtubeUrl) {
        $this->youtubeUrl = $youtubeUrl;

        return $this;
    }

    /**
     * Get youtubeUrl
     *
     * @return string 
     */
    public function getYoutubeUrl() {
        return $this->youtubeUrl;
    }

    /**
     * Set peopleNo
     *
     * @param integer $peopleNo
     * @return Recipe
     */
    public function setPeopleNo($peopleNo) {
        $this->peopleNo = $peopleNo;

        return $this;
    }

    /**
     * Get peopleNo
     *
     * @return integer 
     */
    public function getPeopleNo() {
        return $this->peopleNo;
    }

    /**
     * Set preparationTime
     *
     * @param \DateTime $preparationTime
     * @return Recipe
     */
    public function setPreparationTime($preparationTime) {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    /**
     * Get preparationTime
     *
     * @return \DateTime 
     */
    public function getPreparationTime() {
        return $this->preparationTime;
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
     * Set cookingTime
     *
     * @param \DateTime $cookingTime
     * @return Recipe
     */
    public function setCookingTime($cookingTime) {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    /**
     * Get cookingTime
     *
     * @return \DateTime 
     */
    public function getCookingTime() {
        return $this->cookingTime;
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Recipe
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted() {
        return $this->deleted;
    }

    /**
     * Set draft
     *
     * @param boolean $draft
     * @return Recipe
     */
    public function setDraft($draft) {
        $this->draft = $draft;

        return $this;
    }

    /**
     * Get draft
     *
     * @return boolean 
     */
    public function getDraft() {
        return $this->draft;
    }

    /**
     * Set post
     *
     * @param \MD\Bundle\CMSBundle\Entity\Post $post
     * @return Recipe
     */
    public function setPost(\MD\Bundle\CMSBundle\Entity\Post $post = null) {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \MD\Bundle\CMSBundle\Entity\Post 
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * Set seo
     *
     * @param \MD\Bundle\CMSBundle\Entity\Seo $seo
     * @return Recipe
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
     * @return Recipe
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
     * Add subCategories
     *
     * @param \MD\Bundle\CMSBundle\Entity\SubCategory $subCategories
     * @return Recipe
     */
    public function addSubCategorie(\MD\Bundle\CMSBundle\Entity\SubCategory $subCategories) {
        $this->subCategories[] = $subCategories;

        return $this;
    }

    /**
     * Remove subCategories
     *
     * @param \MD\Bundle\CMSBundle\Entity\SubCategory $subCategories
     */
    public function removeSubCategorie(\MD\Bundle\CMSBundle\Entity\SubCategory $subCategories) {
        $this->subCategories->removeElement($subCategories);
    }

    /**
     * Get subCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubCategories() {
        return $this->subCategories;
    }

    /**
     * Add categories
     *
     * @param \MD\Bundle\CMSBundle\Entity\Category $categories
     * @return Recipe
     */
    public function addCategorie(\MD\Bundle\CMSBundle\Entity\Category $categories) {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \MD\Bundle\CMSBundle\Entity\Category $categories
     */
    public function removeCategorie(\MD\Bundle\CMSBundle\Entity\Category $categories) {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories() {
        return $this->categories;
    }

    /**
     * Is ecpieHasSubCategory
     *
     * @return $subCategoryId
     */
    public function isRecpieHasSubCategory($subCategoryId) {
        $check = $this->subCategories->filter(function($subCategory) use ($subCategoryId) {
            if ($subCategory->getId() == $subCategoryId) {
                return true;
            }
        });
        if (count($check) == 0) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Is recpieHasCategory
     *
     * @return $categoryId
     */
    public function isRecpieHasCategory($categoryId) {
        $check = $this->categories->filter(function($category) use ($categoryId) {
            if ($category->getId() == $categoryId) {
                return true;
            }
        });
        if (count($check) == 0) {
            return FALSE;
        }
        return TRUE;
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
     * Add recipeComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\RecipeComment $recipeComments
     * @return Recipe
     */
    public function addRecipeComment(\MD\Bundle\CMSBundle\Entity\RecipeComment $recipeComments) {
        $this->recipeComments[] = $recipeComments;

        return $this;
    }

    /**
     * Remove recipeComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\RecipeComment $recipeComments
     */
    public function removeRecipeComment(\MD\Bundle\CMSBundle\Entity\RecipeComment $recipeComments) {
        $this->recipeComments->removeElement($recipeComments);
    }

    /**
     * Get recipeComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipeComments() {
        return $this->recipeComments;
    }

    /**
     * Set recipeFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\RecipeFavorite $recipeFavorite
     * @return Recipe
     */
    public function setRecipeFavorite(\MD\Bundle\CMSBundle\Entity\RecipeFavorite $recipeFavorite = null) {
        $this->recipeFavorite = $recipeFavorite;

        return $this;
    }

    /**
     * Get recipeFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\RecipeFavorite 
     */
    public function getRecipeFavorite() {
        return $this->recipeFavorite;
    }

    /**
     * Get GeneralRationg
     *
     * @return INT General Rationg
     */
    public function getGeneralRationg() {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository('CMSBundle:Recipe')->getGeneralRationgByRecipeId($this->getId());
    }

    /**
     * Get checkResipeFavByUserId
     *
     * @return Bool
     */
    public function isResipeFavByUserId($userId) {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository('CMSBundle:Recipe')->checkResipeFavByUserId($this->getId(), $userId);
    }

    /**
     * Add menuPlannerHasRecipes
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlannerHasRecipe $menuPlannerHasRecipes
     * @return Recipe
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

}
