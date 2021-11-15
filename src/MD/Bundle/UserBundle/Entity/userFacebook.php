<?php

namespace MD\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * userFacebook
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class userFacebook {

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="Account", inversedBy="userFacebook")
     * @ORM\JoinColumn(name="id" ,referencedColumnName="id")
     * */
    private $id;

    /**
     * @ORM\Column(name="facebook_id",type="string", length=100)
     */
    private $facebookId;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return userFacebook
     */
    public function setFacebookId($facebookId) {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId() {
        return $this->facebookId;
    }

    /**
     * Set id
     *
     * @param \MD\Bundle\UserBundle\Entity\Account $id
     * @return userFacebook
     */
    public function setId(\MD\Bundle\UserBundle\Entity\Account $id) {
        $this->id = $id;

        return $this;
    }

}
