<?php

namespace MD\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * userFacebook
 *
 * @ORM\Table(name="article_favorite")
 * @ORM\Entity
 */
class ArticleFavorite {

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="Article", inversedBy="articleFavorite")
     * @ORM\JoinColumn(name="article_id" ,referencedColumnName="id")
     * */
    protected $article;

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="articleFavorite")
     * @ORM\JoinColumn(name="person_id" ,referencedColumnName="id")
     * */
    protected $person;

    /**
     * Set article
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $article
     * @return ArticleFavorite
     */
    public function setArticle(\MD\Bundle\CMSBundle\Entity\Article $article) {
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
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return ArticleFavorite
     */
    public function setPerson(\MD\Bundle\UserBundle\Entity\Person $person) {
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

}
