<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\ECommerceBundle\Entity\Currency;
use MD\Utils\SQL;

class FadfadaRepository extends EntityRepository {

    public function checkFadfadaFavByUserId($fadfadaId, $userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM fadfada_favorite WHERE person_id =:userId AND fadfada_id=:fadfadaId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("fadfadaId", $fadfadaId);
        $statement->bindValue("userId", $userId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        return TRUE;
    }

    public function getFavFadfadaByUserId($userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT a.id FROM fadfada a "
                . "LEFT OUTER JOIN fadfada_favorite af ON a.id=af.fadfada_id "
                . "WHERE af.person_id = :userId AND a.publish = :publish ";

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->bindValue("publish", TRUE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Fadfada')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getGeneralRationgByFadfadaId($fadfadaId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT SUM(rating)/COUNT(*) rating FROM fadfada_comment WHERE publish =1 AND fadfada_id = :fadfadaId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("fadfadaId", $fadfadaId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        return $queryResult['rating'];
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';

        if (!isset($search->publish)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= $where . " ( f.publish = TRUE )";
        }



        $clause .= ' GROUP BY f.id ';


        if ($count) {


            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT f.id FROM fadfada f "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT f.id, COUNT(fc.fadfada_id) `count` FROM fadfada f LEFT OUTER JOIN fadfada_comment fc ON fc.fadfada_id=f.id  ";
        $sql .= $clause;
        $sql .= " ORDER BY count DESC";

        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Fadfada')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
