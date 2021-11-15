<?php

namespace MD\Bundle\CMSBundle\Entity;

use MD\Utils\General;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Seo
 * @ORM\Table("seo")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\SeoRepository")
 */
class Seo {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|translation
     * 
     * @Assert\NotBlank()
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @var string|translation
     * 
     * @ORM\Column(name="meta_tag", type="text" , nullable=true)
     */
    protected $metaTag;

    /**
     * @ORM\OneToOne(targetEntity="DynamicPage", mappedBy="seo")
     */
    protected $dynamicPage;

    /**
     * @ORM\OneToOne(targetEntity="Recipe", mappedBy="seo")
     */
    protected $recipe;

    /**
     * @ORM\OneToOne(targetEntity="Article", mappedBy="seo")
     */
    protected $article;

    /**
     * @ORM\OneToOne(targetEntity="SuperCategory", mappedBy="seo")
     */
    protected $superCategory;

    /**
     * @ORM\OneToOne(targetEntity="Category", mappedBy="seo")
     */
    protected $category;

    /**
     * @ORM\OneToOne(targetEntity="SubCategory", mappedBy="seo")
     */
    protected $subCategory;

    /**
     * @ORM\OneToOne(targetEntity="MD\Bundle\UserBundle\Entity\Person", mappedBy="seo")
     */
    protected $person;

    /**
     * @ORM\OneToOne(targetEntity="MD\Bundle\UserBundle\Entity\MenuPlanner", mappedBy="seo")
     */
    protected $menuPlanner;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Seo
     */
    public function setSlug($slug) {
        if ($slug != NULL AND strstr($slug, '/') != FALSE) {
            $slug = explode('/', $slug);
            $this->slug = $slug[0] . '/' . General::seoUrl($slug[1]);
        } else {
            $this->slug = $slug;
        }
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getRawSlug() {
        return $this->slug;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug() {
        if ($this->slug != NULL AND strstr($this->slug, '/') != FALSE) {
            $slug = explode('/', $this->slug);
            return $slug[1];
        } else {
            return $this->slug;
        }
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Seo
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
     * Set metaTag
     *
     * @param string $metaTag
     * @return Seo
     */
    public function setMetaTag($metaTag) {
        $this->metaTag = $metaTag;

        return $this;
    }

    /**
     * Get metaTag
     *
     * @return string 
     */
    public function getMetaTag() {
        return $this->metaTag;
    }

    /**
     * Set dynamicPage
     *
     * @param \MD\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage
     * @return Seo
     */
    public function setDynamicPage(\MD\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage = null) {
        $this->dynamicPage = $dynamicPage;

        return $this;
    }

    /**
     * Get dynamicPage
     *
     * @return \MD\Bundle\CMSBundle\Entity\DynamicPage 
     */
    public function getDynamicPage() {
        return $this->dynamicPage;
    }

    /**
     * Set recipe
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipe
     * @return Seo
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

    /**
     * Set article
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $article
     * @return Seo
     */
    public function setArticle(\MD\Bundle\CMSBundle\Entity\Article $article = null) {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \MD\Bundle\CMSBundle\Entity\Article 
     */
    public function getArticle() {
        return $this->article;
    }

    /**
     * Set superCategory
     *
     * @param \MD\Bundle\CMSBundle\Entity\SuperCategory $superCategory
     * @return Seo
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
     * Set category
     *
     * @param \MD\Bundle\CMSBundle\Entity\Category $category
     * @return Seo
     */
    public function setCategory(\MD\Bundle\CMSBundle\Entity\Category $category = null) {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \MD\Bundle\CMSBundle\Entity\Category 
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Set subCategory
     *
     * @param \MD\Bundle\CMSBundle\Entity\SubCategory $subCategory
     * @return Seo
     */
    public function setSubCategory(\MD\Bundle\CMSBundle\Entity\SubCategory $subCategory = null) {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * Get subCategory
     *
     * @return \MD\Bundle\CMSBundle\Entity\SubCategory 
     */
    public function getSubCategory() {
        return $this->subCategory;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return Seo
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
     * Set menuPlanner
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanner
     * @return Seo
     */
    public function setMenuPlanner(\MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanner = null) {
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

}
