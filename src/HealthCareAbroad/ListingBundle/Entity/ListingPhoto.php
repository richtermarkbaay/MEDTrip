<?php

namespace HealthCareAbroad\ListingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\ListingBundle\Entity\ListingPhoto
 */
class ListingPhoto
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var bigint $listing_id
     */
    private $listing_id;

    /**
     * @var string $filename
     */
    private $filename;

    /**
     * @var string $caption
     */
    private $caption;

    /**
     * @var datetime $date_created
     */
    private $date_created;

    /**
     * @var smallint $status
     */
    private $status;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set listing_id
     *
     * @param bigint $listingId
     * @return ListingPhoto
     */
    public function setListingId($listingId)
    {
        $this->listing_id = $listingId;
        return $this;
    }

    /**
     * Get listing_id
     *
     * @return bigint 
     */
    public function getListingId()
    {
        return $this->listing_id;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return ListingPhoto
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
     * @return ListingPhoto
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
     * Set date_created
     *
     * @param datetime $dateCreated
     * @return ListingPhoto
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;
        return $this;
    }

    /**
     * Get date_created
     *
     * @return datetime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return ListingPhoto
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