<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\ECommerceBundle\Entity\Currency;
use MD\Utils\SQL;

class RecipeRepository extends EntityRepository {

    public function findAll() {
        $connection = $this->getEntityManager();
        $query = $connection->getRepository('CMSBundle:Recipe')->findBy(array('deleted' => FALSE));
        return $query;
    }

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('recipe/' . $slug);
        $entity = $entity->getRecipe();
        return $entity;
    }

    public function checkResipeFavByUserId($recipeId, $userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM recipe_favorite WHERE person_id =:userId AND recipe_id=:recipeId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("recipeId", $recipeId);
        $statement->bindValue("userId", $userId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        return TRUE;
    }

    public function getTopUserByRecipeCount($limit = 20, $currentPersonId = NULL) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT COUNT(*) `count`, person_id FROM recipe "
                . "WHERE deleted = :deleted AND publish = :publish AND draft=:draft  AND person_id != 1";
        if ($currentPersonId !== NULL) {
            $sql .=" AND person_id != " . $currentPersonId;
        }

        $sql.= " GROUP BY person_id "
                . "ORDER BY `count` DESC "
                . "LIMIT " . $limit;

        $statement = $connection->prepare($sql);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $object = $this->getEntityManager()->getRepository('UserBundle:Person')->find($value['person_id']);
            $object->recipeCount = $value['count'];
            $result[] = $object;
        }

        return $result;
    }

    public function getFavRecipeByUserId($userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT r.id FROM recipe r "
                . "LEFT OUTER JOIN recipe_favorite rf ON r.id=rf.recipe_id "
                . "WHERE rf.person_id = :userId AND r.deleted = :deleted AND r.publish = :publish AND r.draft=:draft";

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getLatestRecipeByUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM recipe "
                . "WHERE person_id = :userId AND publish = :publish AND deleted= :deleted AND draft = :draft "
                . "ORDER BY id DESC "
                . "LIMIT " . $limit;

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getMostViewRecipeByUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM recipe "
                . "WHERE person_id = :userId AND publish = :publish AND deleted= :deleted AND draft = :draft "
                . "ORDER BY views DESC "
                . "LIMIT " . $limit;

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        if ($limit == 1) {
            return $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($queryResult[0]['id']);
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getLatestRecipeByNotEqualUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM recipe "
                . "WHERE person_id != :userId AND publish = :publish AND deleted= :deleted AND draft = :draft "
                . "ORDER BY id DESC "
                . "LIMIT " . $limit;

        $statement = $connection->prepare($sql);
        $statement->bindValue("userId", $userId);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getSimilar($recipeId, $categoryId) {
        $connection = $this->getEntityManager()->getConnection();

        $category = "";
        if ($categoryId != 0) {
            $category = "AND rc.subcategory_id=" . $categoryId;
        }

        $sql = "SELECT r.id FROM recipe r LEFT OUTER JOIN recipe_subcategory rc ON r.id=rc.recipe_id WHERE r.publish =:publish AND r.deleted = :deleted AND r.draft=:draft AND r.id != :recipeId " . $category
                . " GROUP BY r.id "
                . "ORDER BY RAND() "
                . "LIMIT 3";

        $statement = $connection->prepare($sql);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->bindValue("recipeId", $recipeId);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getGeneralRationgByRecipeId($recipeId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT SUM(rating)/COUNT(*) rating FROM recipe_comment WHERE publish =1 AND rating IS NOT NULL AND recipe_id = :recipeId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("recipeId", $recipeId);
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

        $where = ($where) ? " AND " : " WHERE ";
        $clause .= $where . " ( r.deleted = False )";
        if (!isset($search->publish)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= $where . " ( r.publish = TRUE )";
        }

        if (!isset($search->draft)) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= $where . " ( r.draft = FALSE )";
        }
        if (isset($search->userId) AND count($search->userId) > 0) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->userId, 'r.person_id', $where);
        }
        if (isset($search->subCategory) AND count($search->subCategory) > 0) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->subCategory, 'rsub.subcategory_id', $where);
        }
        if (isset($search->category) AND count($search->category) > 0) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->category, 'rc.category_id', $where);
        }

        if (isset($search->string) AND $search->string) {
            $search->stringFiltered = substr($connection->quote($search->string), 1, -1);
            if (SQL::validateSS($search->string)) {
                $where = ($where) ? ' AND ( ' : ' WHERE ( ';
                $clause .= SQL::searchSCG($search->stringFiltered, 'r.id', $where);
                $clause .= SQL::searchSCG($search->stringFiltered, 'r.title', ' OR ');
                $clause .= SQL::searchSCG($search->stringFiltered, 'po.content', ' OR ');
                $clause.= " ) ";
            }
        }

        $clause .= ' GROUP BY r.id ';
        $clause .= ' ORDER BY r.id DESC';


        if ($count) {
            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT r.id FROM recipe r "
                    . "LEFT OUTER JOIN recipe_subcategory rsub ON rsub.recipe_id=r.id "
                    . "LEFT OUTER JOIN recipe_category rc ON rc.recipe_id=r.id "
                    . "LEFT OUTER JOIN post po ON po.id=r.post_id "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT r.id FROM recipe r "
                . "LEFT OUTER JOIN recipe_subcategory rsub ON rsub.recipe_id=r.id "
                . "LEFT OUTER JOIN recipe_category rc ON rc.recipe_id=r.id "
                . "LEFT OUTER JOIN post po ON po.id=r.post_id ";
        $sql .= $clause;
        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Recipe')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
