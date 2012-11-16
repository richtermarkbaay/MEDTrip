<?php

namespace HealthCareAbroad\MediaBundle\Entity;

use Imagine\Image\Box;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\MediaBundle\Entity\Media
 */
class Media
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $uuid
     */
    private $uuid;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $caption
     */
    private $caption;

    /**
     * @var string $context
     */
    private $context;

    /**
     * @var string $contentType
     */
    private $contentType;

    /**
     * @var text $metadata
     */
    private $metadata;

    /**
     * @var integer $width
     */
    private $width;

    /**
     * @var integer $height
     */
    private $height;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var datetime $dateModified
     */
    private $dateModified;

    /**
     * @var \HealthCareAbroad\MediaBundle\Entity\Gallery
     */
    private $gallery;

    public function __construct()
    {
    }

    /**
     * Get id
     *
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     * @return Media
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Media
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set context
     *
     * @param string $context
     * @return Media
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set contentType
     *
     * @param string $contentType
     * @return Media
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set metadata
     *
     * @param text $metadata
     * @return Media
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get metadata
     *
     * @return text
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Media
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Media
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return Media
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return datetime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return Media
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
        return $this;
    }

    /**
     * Get dateModified
     *
     * @return datetime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set gallery
     *
     * @return \HealthCareAbroad\MediaBundle\Entity\Media
     */
    public function setGallery(\HealthCareAbroad\MediaBundle\Entity\Gallery $gallery)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Get gallery
     *
     * @return \HealthCareAbroad\MediaBundle\Entity\Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    public function getBox()
    {
        return new Box($this->width, $this->height);
    }
}