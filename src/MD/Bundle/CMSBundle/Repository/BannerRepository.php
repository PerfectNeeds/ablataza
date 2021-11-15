<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository {

    public function getRandBanner($placment, $limit) {
        $connection = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM banner WHERE placement = :placement ORDER BY RAND() limit $limit";
        $statement = $connection->prepare($sql);
        $statement->bindValue("placement", $placment);
        $statement->execute();
        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return NULL;
        }
        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('CMSBundle:Banner')->find($r['id']);
        }
        return $result;
    }

}
