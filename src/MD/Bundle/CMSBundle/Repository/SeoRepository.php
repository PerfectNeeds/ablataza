<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;

class SeoRepository extends EntityRepository {

    public function queryBySlugAndNotId($slug, $notId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM seo WHERE slug = :slug AND id != :notId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->bindValue("notId", $notId);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (!$queryResult) {
            return FALSE;
        }
        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('CMSBundle:Seo')->find($r['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function queryOneBySlug($slug) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM seo WHERE slug = :slug ";

        $statement = $connection->prepare($sql);
        $statement->bindValue("slug", $slug);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        $result = array();
        $result = $this->getEntityManager()->getRepository('CMSBundle:Seo')->find($queryResult['id']);

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

}
