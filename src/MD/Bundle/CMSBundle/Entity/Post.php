<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 * @ORM\Table("post")
 * @ORM\Entity
 */
class Post {

    const Flaged = 1;
    const NotFlaged = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var object|translation
     *
     * @ORM\Column(name="content", type="json_array")
     */
    protected $content;

    /**
     *
     * @ORM\Column(name="flag", type="boolean", options={"default" = 0}))
     */
    protected $flag = false;

    /**
     * @ORM\ManyToMany(targetEntity="\MD\Bundle\MediaBundle\Entity\Image")
     */
    protected $images;

    /**
     * @ORM\ManyToMany(targetEntity="\MD\Bundle\MediaBundle\Entity\Document")
     */
    protected $documents;

    /**
     * @ORM\OneToOne(targetEntity="DynamicPage", mappedBy="post")
     */
    protected $dynamicPage;

    /**
     * @ORM\OneToOne(targetEntity="Recipe", mappedBy="post")
     */
    protected $recipe;

    /**
     * @ORM\OneToOne(targetEntity="Article", mappedBy="post")
     */
    protected $article;

    /**
     * Get Main Image
     *
     * @return MD\Bundle\MediaBundle\Entity\Image
     */
    public function getMainImage() {
        return $this->getImages(array(\MD\Bundle\MediaBundle\Entity\Image::TYPE_MAIN))->first();
    }

    /**
     * Add images
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $images
     * @return DynamicPage
     */
    public function addImage(\MD\Bundle\MediaBundle\Entity\Image $images) {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $images
     */
    public function removeImage(\MD\Bundle\MediaBundle\Entity\Image $images) {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages($types = null) {
        if ($types) {
            return $this->images->filter(function(\MD\Bundle\MediaBundle\Entity\Image $image) use ($types) {
                        return in_array($image->getImageType(), $types);
                    });
        } else {
            return $this->images;
        }
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set content
     *
     * @param array $content
     * @return Post
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return array 
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set flag
     *
     * @param boolean $flag
     * @return Post
     */
    public function setFlag($flag) {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return boolean 
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * Add documents
     *
     * @param \MD\Bundle\MediaBundle\Entity\Document $documents
     * @return Post
     */
    public function addDocument(\MD\Bundle\MediaBundle\Entity\Document $documents) {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \MD\Bundle\MediaBundle\Entity\Document $documents
     */
    public function removeDocument(\MD\Bundle\MediaBundle\Entity\Document $documents) {
        $this->documents->removeElement($documents);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDocuments() {
        return $this->documents;
    }

    /**
     * Set dynamicPage
     *
     * @param \MD\Bundle\CMSBundle\Entity\DynamicPage $dynamicPage
     * @return Post
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
     * @return Post
     */
    public function setRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipe = null)
    {
        $this->recipe = $recipe;
    
        return $this;
    }

    /**
     * Get recipe
     *
     * @return \MD\Bundle\CMSBundle\Entity\Recipe 
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set article
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $article
     * @return Post
     */
    public function setArticle(\MD\Bundle\CMSBundle\Entity\Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \MD\Bundle\CMSBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }
}