<?php
namespace HealthCareAbroad\TreatmentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class SubSpecialization
{
    const STATUS_ACTIVE = 1;

    const STATUS_INACTIVE = 0;
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var datetime $dateModified
     */
    private $dateModified;

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
     * @var HealthCareAbroad\TreatmentBundle\Entity\Specialization
     */
    private $specialization;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $treatments;
    
    public function __construct()
    {
        $this->treatments = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set name
     *
     * @param string $name
     * @return SubSpecialization
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
     * Set description
     *
     * @param text $description
     * @return SubSpecialization
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return SubSpecialization
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
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return SubSpecialization
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
     * @return SubSpecialization
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
     * @return SubSpecialization
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

    /**
     * Set specialization
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     * @return SubSpecialization
     */
    public function setSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization = null)
    {
        $this->specialization = $specialization;
        return $this;
    }

    /**
     * Get specialization
     *
     * @return HealthCareAbroad\TreatmentBundle\Entity\Specialization 
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }
    
    /**
     * Add treatments
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments
     * @return SubSpecialization
     */
    public function addTreatment(\HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments)
    {
        $this->treatments[] = $treatments;
        return $this;
    }

    /**
     * Remove treatments
     *
     * @param HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments
     */
    public function removeTreatment(\HealthCareAbroad\TreatmentBundle\Entity\Treatment $treatments)
    {
        $this->treatments->removeElement($treatments);
    }

    /**
     * Get treatments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTreatments()
    {
        return $this->treatments;
    }
    
    /** custom methods below **/
    
    public function __toString()
    {
        return $this->name;
    }
    
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }
    
    /** end custom methods**/
}