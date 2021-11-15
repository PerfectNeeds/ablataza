<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * SuperCategory
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table("super_category")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\SuperCategoryRepository")
 */
class SuperCategory {

    const TYPE_RECIPE = 1;
    const TYPE_ARTICLE = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="superCategory")
     */
    protected $seo;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     */
    protected $name;

    /**
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    protected $type;

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
     * @ORM\OneToMany(targetEntity="Tip", mappedBy="superCategory")
     */
    protected $tips;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="superCategory")
     */
    protected $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="superCategories")
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

    public function __toString() {
        return $this->getName();
    }

    /**
     * Constructor
     */
    public function __construct() {
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
     * Set name
     *
     * @param string $name
     * @return SuperCategory
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
     * @return SuperCategory
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
     * Set post
     *
     * @param \MD\Bundle\CMSBundle\Entity\Post $post
     * @return SuperCategory
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
     * @return SuperCategory
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
     * Add categories
     *
     * @param \MD\Bundle\CMSBundle\Entity\Category $categories
     * @return SuperCategory
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
        global $kernel;
        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        return $this->categories = $em->getRepository('CMSBundle:Category')->findBy(array('superCategory' => $this->getId(), 'deleted' => FALSE));
    }

    /**
     * Add articles
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $articles
     * @return SuperCategory
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

    /**
     * Set type
     *
     * @param integer $type
     * @return SuperCategory
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType() {
        return $this->type;
    }


    /**
     * Add tips
     *
     * @param \MD\Bundle\CMSBundle\Entity\Tip $tips
     * @return SuperCategory
     */
    public function addTip(\MD\Bundle\CMSBundle\Entity\Tip $tips)
    {
        $this->tips[] = $tips;
    
        return $this;
    }

    /**
     * Remove tips
     *
     * @param \MD\Bundle\CMSBundle\Entity\Tip $tips
     */
    public function removeTip(\MD\Bundle\CMSBundle\Entity\Tip $tips)
    {
        $this->tips->removeElement($tips);
    }

    /**
     * Get tips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTips()
    {
        return $this->tips;
    }
}