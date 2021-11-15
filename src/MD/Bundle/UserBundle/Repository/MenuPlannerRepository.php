<?php

namespace MD\Bundle\UserBundle\Repository;

use MD\Utils\SQL;
use MD\Utils\Validate;
use Doctrine\ORM\EntityRepository;

class MenuPlannerRepository extends EntityRepository {

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('menu-planner/' . $slug);

        if (!$entity) {
            return NULL;
        }
        return $entity->getMenuPlanner();
    }

    public function filter($search, $count = FALSE, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';


        if (isset($search->userId) AND count($search->userId) > 0) {
            $clause .= SQL::inCreate($search->userId, 'person_id', $where);
        }

        $clause .= ' GROUP BY id ';

        if ($count) {
            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT id FROM menu_planner  "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT id FROM menu_planner ";
        $sql .= $clause;

        if ($startLimit !== NULL AND $endLimit !== NULL) {
            $sql .= " LIMIT " . $startLimit . ", " . $endLimit;
        }

        $statement = $connection->prepare($sql);
        $statement->execute();
        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('UserBundle:MenuPlanner')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

    public function filterRecipe($search, $count = FALSE, $sort = 1, $startLimit = NULL, $endLimit = NULL) {
        $connection = $this->getEntityManager()->getConnection();
        $where = FALSE;
        $clause = '';
        $sortOption = array(
            1 => 'mphr.created DESC',
            2 => 'r.title ASC',
            3 => 'r.cooking_time ASC',
        );

        if (isset($search->userId) AND count($search->userId) > 0) {
//            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->userId, 'mp.person_id', $where);
        }
        if (isset($search->menuPlannerId) AND count($search->menuPlannerId) > 0) {
//            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->menuPlannerId, 'mp.id', $where);
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
        if ($sort != NULL AND array_key_exists($sort, $sortOption)) {
            $clause .= ' ORDER BY ' . $sortOption[$sort];
        }

        if ($count) {
            $sql = " SELECT COUNT(x.id) AS `count` FROM ( "
                    . "SELECT r.id FROM recipe r "
                    . "RIGHT OUTER JOIN menu_planner_has_recipe mphr ON r.id=mphr.recipe_id "
                    . "LEFT OUTER JOIN menu_planner mp ON mphr.menuPlanner_id=mp.id "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT r.id FROM recipe r "
                . "RIGHT OUTER JOIN menu_planner_has_recipe mphr ON r.id=mphr.recipe_id "
                . "LEFT OUTER JOIN menu_planner mp ON mphr.menuPlanner_id=mp.id ";
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
