<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("category")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\CategoryRepository")
 */
class Category {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="category")
     */
    protected $seo;

    /**
     * @ORM\ManyToOne(targetEntity="SuperCategory", inversedBy="categories")
     */
    protected $superCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     */
    protected $name;

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
     * @ORM\OneToMany(targetEntity="SubCategory", mappedBy="category")
     */
    protected $subCategories;

    /**
     * @ORM\ManyToMany(targetEntity="Recipe", mappedBy="categories")
     */
    protected $recipes;

    /**
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="categories")
     */
    protected $articles;

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
     * @return Category
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Category
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
     * Get created
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
     * Set seo
     *
     * @param \MD\Bundle\CMSBundle\Entity\Seo $seo
     * @return Category
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
     * Set superCategory
     *
     * @param \MD\Bundle\CMSBundle\Entity\SuperCategory $superCategory
     * @return Category
     */
    public function setSuperCategory(\MD\Bundle\CMSBundle\Entity\SuperCategory $superCategory = null) {
        $this->superCategory = $superCategory;

        return $this;
    }

    /**
     * Get superCategory
     *
     * @return \MD\Bundle\CMSBundle\Entity\SuperCategory 
     */
    public function getSuperCategory() {
        return $this->superCategory;
    }

    /**
     * Add subCategories
     *
     * @param \MD\Bundle\CMSBundle\Entity\SubCategory $subCategories
     * @return Category
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
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        return $this->subCategories = $em->getRepository('CMSBundle:SubCategory')->findBy(array('category' => $this->getId(), 'deleted' => FALSE));
    }

    /**
     * Add recipes
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipes
     * @return Category
     */
    public function addRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipes) {
        $this->recipes[] = $recipes;

        return $this;
    }

    /**
     * Remove recipes
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipes
     */
    public function removeRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipes) {
        $this->recipes->removeElement($recipes);
    }

    /**
     * Get recipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipes() {
        return $this->recipes;
    }

    /**
     * Add articles
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $articles
     * @return SubCategory
     */
    public function addArticle(\MD\Bundle\CMSBundle\Entity\Article $articles) {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $articles
     */
    public function removeArticle(\MD\Bundle\CMSBundle\Entity\Article $articles) {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles() {
        return $this->articles;
    }

}
