<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionUser
 */
class InstitutionUser
{
    /**
     * @var bigint $accountId
     */
    private $accountId;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserType
     */
    private $institutionUserType;


    /**
     * Get accountId
     *
     * @return bigint 
     */
    public function getAccountId()
    {
        return $this->accountId;
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
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserType $institutionUserType
     * @return InstitutionUser
     */
    public function setInstitutionUserType(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserType $institutionUserType = null)
    {
        $this->institutionUserType = $institutionUserType;
        return $this;
    }

    /**
     * Get institutionUserType
     *
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserType 
     */
    public function getInstitutionUserType()
    {
        return $this->institutionUserType;
    }
}