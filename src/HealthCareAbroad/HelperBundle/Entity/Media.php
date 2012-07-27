<?php

namespace HealthCareAbroad\HelperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\HelperBundle\Entity\Media
 */
class Media
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $filename
     */
    private $filename;

    /**
     * @var string $caption
     */
    private $caption;

    /**
     * @var smallint $type
     */
    private $type;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var smallint $status
     */
    private $status;


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
     * Set filename
     *
     * @param string $filename
     * @return Media
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
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
     * Set type
     *
     * @param smallint $type
     * @return Media
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return smallint 
     */
    public function getType()
    {
        return $this->type;
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
     * Set slug
     *
     * @param string $slug
     * @return Media
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return Media
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }
}