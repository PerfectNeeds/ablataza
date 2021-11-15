<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\ECommerceBundle\Entity\Currency;
use MD\Utils\SQL;

class QuoteRepository extends EntityRepository {

    public function findOneRand() {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM quote WHERE publish =1  "
                . "ORDER BY RAND() "
                . "LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        $result = $this->getEntityManager()->getRepository('CMSBundle:Quote')->find($queryResult['id']);

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';

        if (!isset($search->publish)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= $where . " ( publish = TRUE )";
        }


        if (isset($search->string) AND $search->string) {
            $search->stringFiltered = substr($connection->quote($search->string), 1, -1);
            if (SQL::validateSS($search->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG($search->stringFiltered, 'quote', $where);
                $clause.= " ) ";
            }
        }

        $clause .= ' GROUP BY id ';


        if ($count) {
            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT id FROM quote  "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT id FROM quote ";
        $sql .= $clause;

        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Quote')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}