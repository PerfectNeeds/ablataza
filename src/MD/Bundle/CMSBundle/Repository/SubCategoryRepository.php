<?php

namespace MD\Bundle\CMSBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MD\Bundle\CMSBundle\Entity\SuperCategory;

class SubCategoryRepository extends EntityRepository {

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('subcategory/' . $slug);
        if (!$entity) {
            return NULL;
        }
        $entity = $entity->getSubCategory();
        return $entity;
    }

    public function getAllSubCategoryBySuperCategoryType($type) {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT subc.id FROM super_category sc "
                . "LEFT OUTER JOIN category c ON c.superCategory_id=sc.id "
                . "LEFT OUTER JOIN sub_category subc ON subc.category_id=c.id "
                . "WHERE subc.deleted=:deleted AND sc.type=:type";

        $statement = $connection->prepare($sql);
        $statement->bindValue("deleted", FALSE);
        $statement->bindValue("type", $type);
        $statement->execute();

        $filterResult = $statement->fetchAll();
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[] = $this->getEntityManager()->getRepository('CMSBundle:SubCategory')->find($r['id']);
        }
//-----------------------------------------------------------------------------------------------------------------------
        return $result;
    }

    public function removeSubCategoriesByRecipeId($recipeId) {
        $sql = "DELETE FROM recipe_subcategory WHERE recipe_id =?";
        $this->getEntityManager()->getConnection()->executeUpdate($sql, array($recipeId));
    }

}
