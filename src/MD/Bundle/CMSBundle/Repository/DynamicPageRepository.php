<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MD\Utils\SQL;

class DynamicPageRepository extends EntityRepository {

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('dynamic-page/' . $slug);
        $entity = $entity->getDynamicPage();
        return $entity;
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';


        if (isset($search->string) AND $search->string) {
            $search->stringFiltered = substr($connection->quote($search->string), 1, -1);
            if (SQL::validateSS($search->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG($search->stringFiltered, 'd.id', $where);
                $clause .= SQL::searchSCG($search->stringFiltered, 'd.title', ' OR ');
                $clause .= SQL::searchSCG($search->stringFiltered, 'pst.content', ' OR ');
                $clause.= " ) ";
            }
        }

        $clause .= ' GROUP BY d.id ';


        if ($count) {
            $sql = "SELECT count(*) count FROM `dynamic_page` d "
                    . "LEFT OUTER JOIN post pst ON pst.id=d.post_id ";
            $sql .= $clause;

            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT d.id FROM `dynamic_page` d "
                . "LEFT OUTER JOIN post pst ON pst.id=d.post_id ";
        $sql .= $clause;

        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }


        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:DynamicPage')->find($r['id']);
        }
        return $result;
    }

}
