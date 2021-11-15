<?php

namespace MD\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner
 *
 * @ORM\Table("banner")
 * @ORM\Entity(repositoryClass="MD\Bundle\CMSBundle\Repository\BannerRepository")
 */
class Banner {

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
     * @ORM\Column(name="title", type="string", length=45)
     */
    protected $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="placement", type="integer")
     */
    protected $placement;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable = true)
     */
    protected $url;

    /**
     * @var string |translation
     * @ORM\Column(name="text", type="string", length=255, nullable = true)
     */
    protected $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="open_type", type="integer")
     */
    protected $openType;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\MediaBundle\Entity\Image", inversedBy="banner")
     */
    protected $image;

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
     * @return Banner
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
     * Set placement
     *
     * @param integer $placement
     * @return Banner
     */
    public function setPlacement($placement) {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return integer 
     */
    public function getPlacement() {
        return $this->placement;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Banner
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Banner
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Set openType
     *
     * @param integer $openType
     * @return Banner
     */
    public function setOpenType($openType) {
        $this->openType = $openType;

        return $this;
    }

    /**
     * Get openType
     *
     * @return integer 
     */
    public function getOpenType() {
        return $this->openType;
    }

    /**
     * Set image
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $image
     * @return Banner
     */
    public function setImage(\MD\Bundle\MediaBundle\Entity\Image $image = null) {

        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \MD\Bundle\MediaBundle\Entity\Image 
     */
    public function getImage() {
        return $this->image;
    }

}