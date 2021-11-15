<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MD\Bundle\CMSBundle\Entity\SuperCategory;

class SuperCategoryRepository extends EntityRepository {

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('supercategory/' . $slug);
        if (!$entity) {
            return NULL;
        }
        $entity = $entity->getSuperCategory();
        return $entity;
    }

    public function getAllBySuperCategoryType($type) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT sc.id FROM super_category sc "
                . "WHERE sc.deleted=:deleted AND sc.type=:type";

        $statement = $connection->prepare($sql);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("type", $type);
        $statement->execute();

        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:SuperCategory')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

    public function removeSuperCategoriesByRecipeId($recipeId) {
        $sql = "DELETE FROM recipe_supercategory WHERE recipe_id =?";
        $this->getEntityManager()->getConnection()->executeUpdate($sql, array($recipeId));
    }

    public function removeSuperCategoriesByArticleId($articleId) {
        $sql = "DELETE FROM article_supercategory WHERE article_id =?";
        $this->getEntityManager()->getConnection()->executeUpdate($sql, array($articleId));
    }

}
