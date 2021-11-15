<?php

namespace MD\Bundle\MediaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("image")
 * @ORM\Entity(repositoryClass="MD\Bundle\MediaBundle\Repository\ImageRepository")
 */
class Image {

    const TYPE_MCE = 100;
    const TYPE_GALLERY = 200;
    const TYPE_MAIN = 201;
    const TYPE_THUMBNAIL = 202;
    const TYPE_FLOORPLAN = 203;
    const TYPE_LOGO = 204;

    private $filenameForRemove;
    private $filenameForRemoveResize;
    private $directoryForRemove;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $extension;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $imageType;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\Banner", mappedBy="image")
     */
    protected $banner;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\Survey", mappedBy="image")
     */
    protected $survey;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\IngredientCategory", mappedBy="image")
     */
    protected $ingredientCategory;

    /**
     *
     * @Assert\NotBlank()

     */
    Protected $file;

    public function __construct() {
        
    }

    public function getWebExtension($directory) {
        return null === $this->extension ? null : $this->getUploadDir($directory) . '/' . $this->extension;
    }

    protected function getUploadRootDir($directory) {
        // the absolute directory extension where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../../' . \AppKernel::webRoot . $this->getUploadDir($directory);
    }

    public function getUploadDirForResize($directory) {
        // the absolute directory extension where uploaded
        // documents should be saved
        return __DIR__ . '/../../../../../' . \AppKernel::webRoot . $this->getUploadDir($directory);
    }

    public function preUpload() {
        if (null !== $this->file) {
            $this->extension = $this->file->guessExtension();
        }
    }

    public function upload($directory) {

        if (null === $this->file) {
            return;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->file->move($this->getUploadRootDir($directory), $this->id);
        unset($this->file);
    }

    public function storeFilenameForRemove($directory) {
        $this->filenameForRemove = $this->getAbsoluteExtension($directory);
    }

    public function removeUpload() {
        if ($this->filenameForRemove) {
            if (file_exists($this->filenameForRemove)) {
                unlink($this->filenameForRemove);
                $this->removeDirectoryUpload(substr($this->filenameForRemove, 0, strrpos($this->filenameForRemove, "/")));
            }
        }
    }

    public function storeDirectoryForRemove($directory) {
        $this->directoryForRemove = $this->getUploadDirForResize($directory);
    }

    public function removeDirectoryUpload($directory = NULL) {
        if ($directory != NULL) {
            if (count(array_diff(scandir($directory), array('.', '..'))) == 0) { //if the directory is empty will remove
                rmdir($directory);
            }
        } elseif ($this->directoryForRemove) {
            rmdir($this->directoryForRemove);
        }
    }

    public function getAbsoluteExtension($directory) {
        return null === $this->extension ? null : $this->getUploadRootDir($directory) . '/' . $this->id;
    }

    public function storeFilenameForResizeRemove($directory) {
        $this->filenameForRemoveResize = $this->getAbsoluteResizeExtension($directory);
    }

    public function removeResizeUpload() {
        if ($this->filenameForRemoveResize) {
            if (file_exists($this->filenameForRemove)) {
                unlink($this->filenameForRemoveResize);
            }
        }
    }

    public function getAbsoluteResizeExtension($directory) {
        return null === $this->extension ? null : $this->getUploadRootDir($directory) . '/' . "th" . $this->id;
    }

    protected function getUploadDir($directory) {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/' . $directory;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set extension
     *
     * @param string $extension
     * @return Image
     */
    public function setExtension($extension) {

        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

    //PETER???????
    public function setFile($file) {
        $this->file = $file;

        return $this;
    }

    //PETER???????
    public function getFile() {
        return $this->file;
    }

    /**
     * Set imageType
     *
     * @param integer $imageType
     * @return Image
     */
    public function setImageType($imageType) {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType
     *
     * @return integer
     */
    public function getImageType() {
        return $this->imageType;
    }

    /**
     * Set dynamicpage
     *
     * @param \MD\Bundle\CMSBundle\Entity\DynamicPage $dynamicpage
     * @return Image
     */
    public function setDynamicpage(\MD\Bundle\CMSBundle\Entity\DynamicPage $dynamicpage = null) {
        $this->dynamicpage = $dynamicpage;

        return $this;
    }

    /**
     * Get dynamicpage
     *
     * @return \MD\Bundle\CMSBundle\Entity\DynamicPage
     */
    public function getDynamicpage() {
        return $this->dynamicpage;
    }

    /**
     * Set banner
     *
     * @param \MD\Bundle\CMSBundle\Entity\Banner $banner
     * @return Image
     */
    public function setBanner(\MD\Bundle\CMSBundle\Entity\Banner $banner = null) {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return \MD\Bundle\CMSBundle\Entity\Banner 
     */
    public function getBanner() {
        return $this->banner;
    }

    /**
     * Set survey
     *
     * @param \MD\Bundle\CMSBundle\Entity\Survey $survey
     * @return Image
     */
    public function setSurvey(\MD\Bundle\CMSBundle\Entity\Survey $survey = null) {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return \MD\Bundle\CMSBundle\Entity\Survey 
     */
    public function getSurvey() {
        return $this->survey;
    }

}
