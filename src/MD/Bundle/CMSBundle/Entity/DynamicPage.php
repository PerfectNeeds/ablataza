<?php

namespace MD\Bundle\CMSBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * DynamicPage
 * @ORM\Table("dynamic_page")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\DynamicPageRepository")
 */
class DynamicPage {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=45)
     */
    protected $title;

    /**
     * @ORM\OneToOne(targetEntity="Seo", inversedBy="dynamicPage")
     */
    protected $seo;

    /**
     * @ORM\OneToOne(targetEntity="Post", inversedBy="dynamicPage")
     */
    protected $post;

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
     * @return DynamicPage
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
     * Set seo
     *
     * @param \MD\Bundle\CMSBundle\Entity\Seo $seo
     * @return DynamicPage
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
     * Set post
     *
     * @param \MD\Bundle\CMSBundle\Entity\Post $post
     * @return DynamicPage
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

}