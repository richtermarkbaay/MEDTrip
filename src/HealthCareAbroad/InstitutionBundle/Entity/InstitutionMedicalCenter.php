<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenter
{
    
    /**
     * @var bigint $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $businessHours
     */
    private $businessHours;

    /**
     * @var text $description
     */
    private $description;

    /**
     * @var datetime $dateCreated
     */
    private $dateCreated;

    /**
     * @var datetime $dateUpdated
     */
    private $dateUpdated;

    /**
     * @var smallint $status
     */
    private $status;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionSpecializations;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $institutionAffiliations;

    /**
     * @var HealthCareAbroad\InstitutionBundle\Entity\Institution
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $doctors;

    public function __construct()
    {
        $this->institutionSpecializations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->institutionAffiliations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doctors = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return InstitutionMedicalCenter
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
     * Set businessHours
     *
     * @param string $businessHours
     * @return InstitutionMedicalCenter
     */
    public function setBusinessHours($businessHours)
    {
        $this->businessHours = $businessHours;
        return $this;
    }

    /**
     * Get businessHours
     *
     * @return string 
     */
    public function getBusinessHours()
    {
        return $this->businessHours;
    }

    /**
     * Set description
     *
     * @param text $description
     * @return InstitutionMedicalCenter
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
     * Set dateCreated
     *
     * @param datetime $dateCreated
     * @return InstitutionMedicalCenter
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
     * Set dateUpdated
     *
     * @param datetime $dateUpdated
     * @return InstitutionMedicalCenter
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return datetime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set status
     *
     * @param smallint $status
     * @return InstitutionMedicalCenter
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
     * Set slug
     *
     * @param string $slug
     * @return InstitutionMedicalCenter
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
     * Add institutionSpecializations
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations
     * @return InstitutionMedicalCenter
     */
    public function addInstitutionSpecialization(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations)
    {
        $this->institutionSpecializations[] = $institutionSpecializations;
        return $this;
    }

    /**
     * Remove institutionSpecializations
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations
     */
    public function removeInstitutionSpecialization(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization $institutionSpecializations)
    {
        $this->institutionSpecializations->removeElement($institutionSpecializations);
    }

    /**
     * Get institutionSpecializations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getInstitutionSpecializations()
    {
        return $this->institutionSpecializations;
    }

    /**
     * Set institution
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\Institution $institution
     * @return institution
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
     * Add doctors
     *
     * @param HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors
     * @return InstitutionMedicalCenter
     */
    public function addDoctor(\HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors)
    {
        $this->doctors[] = $doctors;
        return $this;
    }

    /**
     * Remove doctors
     *
     * @param HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors
     */
    public function removeDoctor(\HealthCareAbroad\DoctorBundle\Entity\Doctor $doctors)
    {
        $this->doctors->removeElement($doctors);
    }
    
    /**
     * Add institutionAffiliations
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Affiliation $institutionAffiliations
     * @return InstitutionAffiliation
     */
    public function addInstitutionAffiliation(\HealthCareAbroad\HelperBundle\Entity\Affiliation $institutionAffiliations)
    {
    	$this->institutionAffiliations[] = $institutionAffiliations;
    	return $this;
    }
    
    /**
     * Remove institutionAffiliations
     *
     * @param HealthCareAbroad\HelperBundle\Entity\Affiliation $institutionAffiliations
     */
    public function removeInstitutionAffiliation(\HealthCareAbroad\HelperBundle\Entity\Affiliation $institutionAffiliation)
    {
    	$this->institutionAffiliations->removeElement($institutionAffiliations);
    }
    
    /**
     * Get institutionAffiliations
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getInstitutionAffiliations()
    {
    	return $this->institutionAffiliations;
    }
    
    /**
     * Get doctors
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDoctors()
    {
        return $this->doctors;
    }
}