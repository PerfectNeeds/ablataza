<?php

namespace MD\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Follower
 *
 * @ORM\Table(name="follower")
 * @ORM\Entity(repositoryClass="MD\Bundle\UserBundle\Repository\FollowerRepository")
 */
class Follower {

    /**
     * @var integer
     * @ORM\id
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="followerPersons")
     * */
    protected $person;

    /**
     * @var integer
     * @ORM\id
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="followers")
     * */
    protected $follower;

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return Follower
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

    /**
     * Set follower
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $follower
     * @return Follower
     */
    public function setFollower(\MD\Bundle\UserBundle\Entity\Person $follower) {
        $this->follower = $follower;

        return $this;
    }

    /**
     * Get follower
     *
     * @return \MD\Bundle\UserBundle\Entity\Person 
     */
    public function getFollower() {
        return $this->follower;
    }

}
