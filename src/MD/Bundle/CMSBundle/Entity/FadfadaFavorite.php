<?php

namespace MD\Bundle\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * userFacebook
 *
 * @ORM\Table(name="fadfada_favorite")
 * @ORM\Entity
 */
class FadfadaFavorite {

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="Fadfada", inversedBy="fadfadaFavorite")
     * @ORM\JoinColumn(name="fadfada_id" ,referencedColumnName="id")
     * */
    protected $fadfada;

    /**
     * @var integer
     * @ORM\id
     * @ORM\OneToOne(targetEntity="\MD\Bundle\UserBundle\Entity\Person", inversedBy="fadfadaFavorite")
     * @ORM\JoinColumn(name="person_id" ,referencedColumnName="id")
     * */
    protected $person;

    /**
     * Set fadfada
     *
     * @param \MD\Bundle\CMSBundle\Entity\Fadfada $fadfada
     * @return FadfadaFavorite
     */
    public function setFadfada(\MD\Bundle\CMSBundle\Entity\Fadfada $fadfada) {
        $this->fadfada = $fadfada;

        return $this;
    }

    /**
     * Get fadfada
     *
     * @return \MD\Bundle\CMSBundle\Entity\Fadfada 
     */
    public function getFadfada() {
        return $this->fadfada;
    }

    /**
     * Set person
     *
     * @param \MD\Bundle\UserBundle\Entity\Person $person
     * @return FadfadaFavorite
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
