<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedia
 */
class InstitutionMedia
{
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var smallint $type
     */
    private $type;

    /**
     * @var HealthCareAbroad\HelperBundle\Entity\Media
     */
    private $media;


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
     * Set type
     *
     * @param smallint $type
     * @return InstitutionMedia
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
     * Set media
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Media $media
     * @return InstitutionMedia
     */
    public function setMedia(\HealthCareAbroad\HelperBundle\Entity\Media $media = null)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * Get media
     *
     * @return HealthCareAbroad\HelperBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }
}