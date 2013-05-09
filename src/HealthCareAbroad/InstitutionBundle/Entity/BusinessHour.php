<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

class BusinessHour
{
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $weekdayBitValue;

    /**
     * @var \DateTime
     */
    private $opening;

    /**
     * @var \DateTime
     */
    private $closing;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;


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
     * Set weekdayBitValue
     *
     * @param integer $weekdayBitValue
     * @return BusinessHour
     */
    public function setWeekdayBitValue($weekdayBitValue)
    {
        $this->weekdayBitValue = $weekdayBitValue;
    
        return $this;
    }

    /**
     * Get weekdayBitValue
     *
     * @return integer 
     */
    public function getWeekdayBitValue()
    {
        return $this->weekdayBitValue;
    }

    /**
     * Set opening
     *
     * @param \DateTime $opening
     * @return BusinessHour
     */
    public function setOpening($opening)
    {
        $this->opening = $opening;
    
        return $this;
    }

    /**
     * Get opening
     *
     * @return \DateTime 
     */
    public function getOpening()
    {
        return $this->opening;
    }

    /**
     * Set closing
     *
     * @param \DateTime $closing
     * @return BusinessHour
     */
    public function setClosing($closing)
    {
        $this->closing = $closing;
    
        return $this;
    }

    /**
     * Get closing
     *
     * @return \DateTime 
     */
    public function getClosing()
    {
        return $this->closing;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return BusinessHour
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set institutionMedicalCenter
     *
     * @param \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter
     * @return BusinessHour
     */
    public function setInstitutionMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $institutionMedicalCenter = null)
    {
        $this->institutionMedicalCenter = $institutionMedicalCenter;
    
        return $this;
    }

    /**
     * Get institutionMedicalCenter
     *
     * @return \HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter 
     */
    public function getInstitutionMedicalCenter()
    {
        return $this->institutionMedicalCenter;
    }
}