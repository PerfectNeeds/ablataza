<?php

namespace MD\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role {

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Account", inversedBy="roles")
     * */
    protected $accounts;

    /**
     * Constructor
     */
    public function __construct() {
        $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Role
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
     * Add accounts
     *
     * @param \MD\Bundle\UserBundle\Entity\Account $accounts
     * @return Role
     */
    public function addAccount(\MD\Bundle\UserBundle\Entity\Account $accounts) {
        $this->accounts[] = $accounts;

        return $this;
    }

    /**
     * Remove accounts
     *
     * @param \MD\Bundle\UserBundle\Entity\Account $accounts
     */
    public function removeAccount(\MD\Bundle\UserBundle\Entity\Account $accounts) {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccounts() {
        return $this->accounts;
    }

}