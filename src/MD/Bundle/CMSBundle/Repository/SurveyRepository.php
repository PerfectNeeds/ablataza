<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\ECommerceBundle\Entity\Currency;
use MD\Utils\SQL;

class SurveyRepository extends EntityRepository {

    public function findOneRand() {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM survey WHERE publish =:publish "
                . "ORDER BY RAND() "
                . "LIMIT 1";

        $statement = $connection->prepare($sql);
        $statement->bindValue("publish", TRUE);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        $result = $this->getEntityManager()->getRepository('CMSBundle:Survey')->find($queryResult['id']);

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function checkPersonHasAnswer($surveyId, $userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT count(id) count FROM survey_answer WHERE survey_id =:surveyId AND person_id=:userId ";

        $statement = $connection->prepare($sql);
        $statement->bindValue("surveyId", $surveyId);
        $statement->bindValue("userId", $userId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        if ($queryResult['count'] > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function getSurveyRate($surveyId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT COUNT(id) count, `value` FROM survey_answer WHERE survey_id = :surveyId "
                . "GROUP BY `value`";

        $statement = $connection->prepare($sql);
        $statement->bindValue("surveyId", $surveyId);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        $return = array();
        foreach ($queryResult as $value) {
            $array[$value['value']] = $value['count'];
        }
        return $array;
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';

        if (!isset($search->publish)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= $where . " ( publish = TRUE )";
        }

        if (isset($search->userId) AND count($search->userId) > 0) {
            $clause .= SQL::inCreate($search->userId, 'person_id', $where);
        }

        if (isset($search->string) AND $search->string) {
            $search->stringFiltered = substr($connection->quote($search->string), 1, -1);
            if (SQL::validateSS($search->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG($search->stringFiltered, 'tip', $where);
                $clause.= " ) ";
            }
        }

        $clause .= ' GROUP BY id ';

        if ($count) {
            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT id FROM survey  "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT id FROM survey ";
        $sql .= $clause;

        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Survey')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
