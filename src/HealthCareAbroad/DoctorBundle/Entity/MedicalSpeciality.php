<?php

namespace HealthCareAbroad\DoctorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalSpeciality
 */
class MedicalSpeciality
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \HealthCareAbroad\TreatmentBundle\Entity\Specialization
     */
    private $specialization;


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
     * @return MedicalSpeciality
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
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return MedicalSpeciality
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return MedicalSpeciality
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set specialization
     *
     * @param \HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization
     * @return MedicalSpeciality
     */
    public function setSpecialization(\HealthCareAbroad\TreatmentBundle\Entity\Specialization $specialization = null)
    {
        $this->specialization = $specialization;
    
        return $this;
    }

    /**
     * Get specialization
     *
     * @return \HealthCareAbroad\TreatmentBundle\Entity\Specialization 
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }
}
