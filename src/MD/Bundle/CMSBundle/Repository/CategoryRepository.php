<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MD\Bundle\CMSBundle\Entity\SuperCategory;

class CategoryRepository extends EntityRepository {

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('category/' . $slug);
        if (!$entity) {
            return NULL;
        }
        $entity = $entity->getCategory();
        return $entity;
    }

    public function getAllCategoryBySuperCategoryType($type) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT c.id FROM super_category sc "
                . "LEFT OUTER JOIN category c ON c.superCategory_id=sc.id "
                . "WHERE c.deleted=:deleted AND sc.type=:type";

        $statement = $connection->prepare($sql);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("type", $type);
        $statement->execute();

        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:Category')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

    public function removeCategoriesByRecipeId($recipeId) {
        $sql = "DELETE FROM recipe_category WHERE recipe_id =?";
        $this->getEntityManager()->getConnection()->executeUpdate($sql, array($recipeId));
    }

    public function removeCategoriesByArticleId($articleId) {
        $sql = "DELETE FROM article_category WHERE article_id =?";
        $this->getEntityManager()->getConnection()->executeUpdate($sql, array($articleId));
    }

}
