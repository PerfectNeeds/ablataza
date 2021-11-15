<?php

namespace MD\Bundle\MediaBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table("video")
 * @ORM\Entity
 */
class Video {

    const YOUTUBE = 1;
    const VIMEO = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="video_id", type="string", length=45)
     */
    private $videoId;

    /**
     * @ORM\Column(name="video_type", type="smallint", nullable=true)
     */
    protected $type;

    /**
     * @var text
     *
     * @ORM\Column(name="note", type="text", nullable = true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="image_path", type="string", length=100)
     */
    private $imagePath;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set videoId
     *
     * @param string $videoId
     * @return Video
     */
    public function setVideoId($videoId) {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Get videoId
     *
     * @return string 
     */
    public function getVideoId() {
        return $this->videoId;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Video
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Photo
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Set imagePath
     *
     * @param string $imagePath
     * @return Video
     */
    public function setImagePath($imagePath) {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * Get imagePath
     *
     * @return string 
     */
    public function getImagePath() {
        return $this->imagePath;
    }

}
