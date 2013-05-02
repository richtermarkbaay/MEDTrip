<?php

namespace HealthCareAbroad\UserBundle\Entity;

use Symfony\Component\Translation\Tests\String;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\UserBundle\Entity\InstitutionUser
 */
class InstitutionUser extends SiteUser 
{
    private $status;
    
    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;
    
    /**
     * @var string $jobTitle
     */
    private $jobTitle;
    
    /**
     * @var \HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;
    
    /**
     * @var \HealthCareAbroad\UserBundle\Entity\InstitutionUserType
     */
    private $institutionUserType;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $contactDetails;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contactDetails = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return InstitutionUser
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
     * Set status
     *
     * @param smallint $status
     * @return InstitutionUser
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
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return InstitutionUser
     */
    public function setInstitution(\HealthCareAbroad\InstitutionBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;
        return $this;
    }
    
    /**
     * Get institution
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }
    
    /**
     * Set institutionUserType
     *
     * @param HealthCareAbroad\UserBundle\Entity\InstitutionUserType $institutionUserType
     * @return InstitutionUser
     */
    public function setInstitutionUserType(\HealthCareAbroad\UserBundle\Entity\InstitutionUserType $institutionUserType = null)
    {
        $this->institutionUserType = $institutionUserType;
        return $this;
    }
    
    /**
     * Get institutionUserType
     *
     * @return HealthCareAbroad\UserBundle\Entity\InstitutionUserType
     */
    public function getInstitutionUserType()
    {
        return $this->institutionUserType;
    }
    
    /**
     * Set jobTitle
     *
     * @param string $jobTitle
     * @return InstitutionUser
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }
    
    /**
     * Get jobTitle
     *
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }
    /**
     * Add contactDetails
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails
     * @return InstitutionUser
     */
    public function addContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails)
    {
        $this->contactDetails[] = $contactDetails;
    
        return $this;
    }

    /**
     * Remove contactDetails
     *
     * @param \HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails
     */
    public function removeContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetails)
    {
        $this->contactDetails->removeElement($contactDetails);
    }

    /**
     * Get contactDetails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactDetails()
    {
        return $this->contactDetails;
    }
}