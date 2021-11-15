<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MD\Bundle\CMSBundle\Entity\Article;
use MD\Utils\SQL;

class ArticleRepository extends EntityRepository {

    public function findAll() {
        $connection = $this->getEntityManager();
        $query = $connection->getRepository('CMSBundle:Article')->findBy(array('deleted' => FALSE));
        return $query;
    }

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('article/' . $slug);
        $entity = $entity->getArticle();
        return $entity;
    }

    public function getMostViewArticleByUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM article "
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
            return $this->getEntityManager()->getRepository('CMSBundle:Article')->find($queryResult[0]['id']);
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getRelatedArticleLimit(Article $article, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT a.id FROM article a "
                . "LEFT OUTER JOIN article_category ac ON ac.article_id=a.id "
                . "WHERE a.publish = :publish AND a.deleted= :deleted AND a.draft = :draft ";

        if (count($article->getCategories()) > 0) {
            $sql .= ' AND ac.category_id= ' . $article->getCategories()[0]->getId();
        }

        $sql .= ' AND a.id !=' . $article->getId();
        $sql.= " ORDER BY RAND() ASC LIMIT " . $limit;

        $statement = $connection->prepare($sql);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->execute();

        $result = array();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return $result;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        return $result;
    }

    public function getLatestArticleByUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM article "
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
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getLatestArticleByNotEqualUserIdAndLimit($userId, $limit = 5) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT id FROM article "
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
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function queryNextArticleBySuperCategoryId($currentId, $applicationId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM article WHERE id > :currentId AND application_id = :applicationId AND deleted = FALSE ORDER BY id DESC Limit 1 ";

        $statement = $connection->prepare($sql);
        $statement->bindValue("currentId", $currentId);
        $statement->bindValue("applicationId", $applicationId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        $result = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($queryResult['id']);

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function queryPrevArticleBySuperCategoryId($currentId, $applicationId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM article WHERE id < :currentId AND application_id = :applicationId AND deleted = FALSE ORDER BY id ASC Limit 1 ";

        $statement = $connection->prepare($sql);
        $statement->bindValue("currentId", $currentId);
        $statement->bindValue("applicationId", $applicationId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        $result = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($queryResult['id']);

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function checkArticleFavByUserId($articleId, $userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM article_favorite WHERE person_id =:userId AND article_id=:articleId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("articleId", $articleId);
        $statement->bindValue("userId", $userId);
        $statement->execute();

        $queryResult = $statement->fetch();
        if (!$queryResult) {
            return FALSE;
        }
        return TRUE;
    }

    public function getFavArticleByUserId($userId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT a.id FROM article a "
                . "LEFT OUTER JOIN article_favorite af ON a.id=af.article_id "
                . "WHERE af.person_id = :userId AND a.deleted = :deleted AND a.publish = :publish AND a.draft=:draft";

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
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getSimilar($articleId, $superCategoryId) {
        $connection = $this->getEntityManager()->getConnection();
        $superCategory = "";
        if ($superCategoryId != 0) {
            $superCategory = "AND `ac`.supercategory_id=" . $superCategoryId;
        }

        $sql = "SELECT a.id FROM article a LEFT OUTER JOIN article_supercategory `ac` ON `ac`.article_id = a.id WHERE a.publish =:publish AND a.deleted = :deleted AND a.draft=:draft AND a.id !=:articleId " . $superCategory
                . " GROUP BY a.id "
                . "ORDER BY RAND() "
                . "LIMIT 3";

        $statement = $connection->prepare($sql);
        $statement->bindValue("publish", TRUE);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("draft", FALSE);
        $statement->bindValue("articleId", $articleId);
        $statement->execute();

        $queryResult = $statement->fetchAll();
        if (count($queryResult) == 0) {
            return FALSE;
        }

        foreach ($queryResult as $value) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($value['id']);
        }

        if ($result == null) {
            return;
        } else {
            return $result;
        }
    }

    public function getGeneralRationgByArticleId($articleId) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT SUM(rating)/COUNT(*) rating FROM article_comment WHERE publish =1 AND rating IS NOT NULL AND article_id = :articleId";

        $statement = $connection->prepare($sql);
        $statement->bindValue("articleId", $articleId);
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
        if (isset($search->superCategory) AND count($search->superCategory) > 0) {
            $where = ($where) ? " AND " : " WHERE ";
            $clause .= SQL::inCreate($search->superCategory, 'rsub.supercategory_id', $where);
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
                    . "SELECT r.id FROM article r "
                    . "LEFT OUTER JOIN article_supercategory rsub ON rsub.article_id=r.id "
                    . "LEFT OUTER JOIN article_category rc ON rc.article_id=r.id "
                    . "LEFT OUTER JOIN post po ON po.id=r.post_id "
                    . $clause . " ) " .
                    " AS x ";
            $statement = $connection->prepare($sql);
            $statement->execute();
            $queryResult = $statement->fetch();

            return (int) $queryResult['count'];
        }
//----------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "SELECT r.id FROM article r "
                . "LEFT OUTER JOIN article_supercategory rsub ON rsub.article_id=r.id "
                . "LEFT OUTER JOIN article_category rc ON rc.article_id=r.id "
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
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Article')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

}
