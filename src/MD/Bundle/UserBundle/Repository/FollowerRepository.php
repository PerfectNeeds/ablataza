<?php

namespace MD\Bundle\UserBundle\Repository;

use MD\Utils\SQL;
use MD\Utils\Validate;
use Doctrine\ORM\EntityRepository;

class FollowerRepository extends EntityRepository {

    public function getFollowerRandLimit($personId, $limit = NULL) {
        $sql = "SELECT * FROM follower WHERE person_id = :personId";

        if ($limit != NULL) {
            $sql .= ' ORDER BY RAND() LIMIT ' . $limit;
        }
        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->bindValue("personId", $personId);
        $statement->execute();
        $queryResult = $statement->fetchAll();
        $result = array();
        foreach ($queryResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Person')->find($r['follower_id']);
        }
        return $result;
    }

    /**
     * get Notification form number of days
     * 
     * @param int $personId
     * @param int $fromDays number of days
     * @return type 
     */
    public function getFollowerNotification($personId, $fromDays = 2) {
        $sql = "SELECT r.id, 'recipe' AS 'type' FROM follower f "
                . "LEFT OUTER JOIN recipe r ON r.person_id = f.person_id "
                . "WHERE f.follower_id =:personId AND r.publish=:publish AND r.deleted=:deleted AND r.draft=:draft AND DATEDIFF(CURDATE(),r.created) <=:fromDays "
                . "UNION ALL "
                . "SELECT a.id, 'article' AS 'type' FROM follower f "
                . "LEFT OUTER JOIN article a ON a.person_id = f.person_id "
                . "WHERE f.follower_id =:personId AND a.publish=:publish AND a.deleted=:deleted AND a.draft=:draft AND DATEDIFF(CURDATE(),a.created) <=:fromDays ";
        $connection = $this->getEntityManager()->getConnection();

        $statement = $connection->prepare($sql);
        $statement->bindValue("personId", $personId);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->bindValue("fromDays", $fromDays);
        $statement->execute();
        $queryResult = $statement->fetchAll();
        $result = array();
        foreach ($queryResult as $key => $r) {
            if ($r['type'] == 'recipe') {
                $result[$key] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($r['id']);
            } elseif ($r['type'] == 'article') {
                $result[$key] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($r['id']);
            }
        }
        return $result;
    }

}
