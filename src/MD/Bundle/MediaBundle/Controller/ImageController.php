<?php

namespace MD\Bundle\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MD\Bundle\MediaBundle\Entity\Image;

/**
 * Image controller.
 *
 * @Route("/image")
 */
class ImageController extends Controller {

    private $type = array(
        1 => 'ad/', //Ad
        2 => 'banner/', //Banner
        3 => 'person/', //Banner
        4 => 'recipe/image/', //Banner
        5 => 'article/image/', //Banner
        6 => 'survey/', //survey
        7 => 'ingredient-category/', //survey
    );

    public function uploadSingleImage($em, $entity, $file, $type) {

        if ($file != null) {
            $uploadPath = $this->type[$type];
            if (method_exists($entity, 'getPost')) {
                $imageId = $entity->getPost()->getId();
                $oldImage = $entity->getPost()->getMainImage();
                if ($oldImage) {
                    $oldImage->storeFilenameForRemove($uploadPath . $entity->getPost()->getId());
                    $oldImage->removeUpload();
                    $em->remove($oldImage);
                    $entity->getPost()->removeImage($oldImage);
                    $em->flush();
                    $em->persist($oldImage);
                    $em->persist($entity);
                    $em->flush();
                }
            } else {
                $imageId = $entity->getId();
                $oldImage = $entity->getImage();
                if ($oldImage) {
                    $oldImage->storeFilenameForRemove($uploadPath . $imageId);
                    $oldImage->removeUpload();
                    $em->remove($oldImage);
                    $em->persist($oldImage);
                    $em->persist($entity);
                }
            }


            $image = new Image();
            $em->persist($image);
            $em->flush();
            $image->setFile($file);
            $image->preUpload();
            $image->upload($uploadPath . $imageId);
            if (method_exists($entity, 'getPost')) {
                $entity->getPost()->addImage($image);
                $image->setImageType(Image::TYPE_MAIN);
            } else {
                $image->setImageType(Image::TYPE_GALLERY);
                $entity->setImage($image);
            }
            $em->persist($entity);
            $em->flush();
        }
    }

}
