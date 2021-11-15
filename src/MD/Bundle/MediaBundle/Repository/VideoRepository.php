<?php

namespace MD\Bundle\MediaBundle\Repository;

use MD\Bundle\MediaBundle\Entity\Image as Image;
use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository {

    /**
     * 
     * @param type $id
     * @param type $sqlTable  table name for many to many relation between video and another entity like ( destination_video  )   
     * @param type $sqlField  filed name like (destination_id)
     */
    public function clearOldVideos($id, $sqlTable, $sqlField) {
        $sql = " DELETE FROM $sqlTable  WHERE $sqlField = ? ";
        $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($id));
    }

}
