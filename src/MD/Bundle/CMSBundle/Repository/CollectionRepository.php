<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CollectionRepository extends EntityRepository {

    public function findAll() {
        $connection = $this->getEntityManager();
        $query = $connection->getRepository('CMSBundle:Collection')->findBy(array('deleted' => FALSE));
        return $query;
    }

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('collection/' . $slug);
        $entity = $entity->getCollection();
        return $entity;
    }

}
