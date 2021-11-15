<?php

namespace MD\Bundle\MediaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("document")
 * @ORM\Entity(repositoryClass="MD\Bundle\MediaBundle\Repository\DocumentRepository")
 */
class Document {

    private $filenameForRemove;

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
     * Store File name
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     *
     * @Assert\NotBlank()

     */
    Protected $file;

    public function __construct($file) {
        $this->setFile($file);
        $this->getOriginalNameWithoutExtension();
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

    public function removeDirectoryUpload($directory) {
        if (count(array_diff(scandir($directory), array('.', '..'))) == 0) { //if the directory is empty will remove
            rmdir($directory);
        }
    }

    public function removeUpload() {
        if ($this->filenameForRemove) {
            if (file_exists($this->filenameForRemove)) {
                unlink($this->filenameForRemove);
                $this->removeDirectoryUpload(substr($this->filenameForRemove, 0, strrpos($this->filenameForRemove, "/")));
            }
        }
    }

    public function getAbsoluteExtension($directory) {
        return null === $this->extension ? null : $this->getUploadRootDir($directory) . '/' . $this->id;
    }

    protected function getUploadDir($directory) {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/' . $directory;
    }

    protected function getOriginalNameWithoutExtension() {
        $originalName = $this->file->getClientOriginalName();
        $originalExtension = $this->file->getClientOriginalExtension();

        $pureName = str_replace('.' . $originalExtension, '', $originalName);
        $this->setName($pureName);
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
     * @return Document
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

    /**
     * Set file
     *
     * @param string $extension
     * @return Document
     */
    public function setFile($file) {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}
