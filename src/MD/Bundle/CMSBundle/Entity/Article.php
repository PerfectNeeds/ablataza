<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recipe
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("article")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\ArticleRepository")
 */
class Article {

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
     * @ORM\OneToOne(targetEntity="Post", inversedBy="article")
     */
    protected $post;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="article")
     */
    protected $seo;

    /**
     * @ORM\ManyToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="articles")
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
     * @ORM\OneToMany(targetEntity="ArticleComment", mappedBy="article", cascade={"all"})
     */
    protected $articleComments;

    /**
     * @ORM\OneToOne(targetEntity="ArticleFavorite", mappedBy="article", cascade={"all"})
     */
    protected $articleFavorite;

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
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="articles")
     */
    protected $categories;

    /**
     * @ORM\ManyToMany(targetEntity="SuperCategory", inversedBy="articles")
     */
    protected $superCategories;

    /**
     * Constructor
     */
    public function __construct() {
        $this->subCategories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->superCategories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Article
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Article
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
     * Set post
     *
     * @param \MD\Bundle\CMSBundle\Entity\Post $post
     * @return Article
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
     * @return Article
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
     * @return Article
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
     * Add superCategories
     *
     * @param \MD\Bundle\CMSBundle\Entity\SuperCategory $superCategories
     * @return Article
     */
    public function addSuperCategorie(\MD\Bundle\CMSBundle\Entity\SuperCategory $superCategories) {
        $this->superCategories[] = $superCategories;

        return $this;
    }

    /**
     * Remove superCategories
     *
     * @param \MD\Bundle\CMSBundle\Entity\SuperCategory $superCategories
     */
    public function removeSuperCategorie(\MD\Bundle\CMSBundle\Entity\SuperCategory $superCategories) {
        $this->superCategories->removeElement($superCategories);
    }

    /**
     * Get superCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSuperCategories() {
        return $this->superCategories;
    }

    /**
     * Is ArticleHasSuperCategory
     *
     * @return $superCategoryId
     */
    public function isArticleHasSuperCategory($superCategoryId) {
        $check = $this->superCategories->filter(function($superCategory) use ($superCategoryId) {
            if ($superCategory->getId() == $superCategoryId) {
                return true;
            }
        });
        if (count($check) == 0) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Is ArticleHasCategory
     *
     * @return $categoryId
     */
    public function isArticleHasCategory($categoryId) {
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

    /**
     * Add categories
     *
     * @param \MD\Bundle\CMSBundle\Entity\Category $categories
     * @return Article
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
     * Add articleComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\ArticleComment $articleComments
     * @return Article
     */
    public function addArticleComment(\MD\Bundle\CMSBundle\Entity\ArticleComment $articleComments) {
        $this->articleComments[] = $articleComments;

        return $this;
    }

    /**
     * Remove articleComments
     *
     * @param \MD\Bundle\CMSBundle\Entity\ArticleComment $articleComments
     */
    public function removeArticleComment(\MD\Bundle\CMSBundle\Entity\ArticleComment $articleComments) {
        $this->articleComments->removeElement($articleComments);
    }

    /**
     * Get articleComments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticleComments() {
        return $this->articleComments;
    }

    /**
     * Set articleFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\ArticleFavorite $articleFavorite
     * @return Article
     */
    public function setArticleFavorite(\MD\Bundle\CMSBundle\Entity\ArticleFavorite $articleFavorite = null) {
        $this->articleFavorite = $articleFavorite;

        return $this;
    }

    /**
     * Get articleFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\ArticleFavorite 
     */
    public function getArticleFavorite() {
        return $this->articleFavorite;
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
        return $em->getRepository('CMSBundle:Article')->getGeneralRationgByArticleId($this->getId());
    }

    /**
     * Get checkArticleFavByUserId
     *
     * @return Bool
     */
    public function isArticleFavByUserId($userId) {
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository('CMSBundle:Article')->checkArticleFavByUserId($this->getId(), $userId);
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

}
