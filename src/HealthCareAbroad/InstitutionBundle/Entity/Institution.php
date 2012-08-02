<?php

namespace HealthCareAbroad\InstitutionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HealthCareAbroad\InstitutionBundle\Entity\Institution
 */
class Institution
{
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
     * @var string $logo
     */
    private $logo;

    /**
     * @var text $address1
     */
    private $address1;

    /**
     * @var text $address2
     */
    private $address2;

    /**
     * @var integer $cityId
     */
    private $cityId;

    /**
     * @var integer $countryId
     */
    private $countryId;

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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $contactDetail;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $medicalCenters;

    public function __construct()
    {
        $this->contactDetail = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medicalCenters = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Institution
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
     * @return Institution
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
     * Set logo
     *
     * @param string $logo
     * @return Institution
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set address1
     *
     * @param text $address1
     * @return Institution
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get address1
     *
     * @return text 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param text $address2
     * @return Institution
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get address2
     *
     * @return text 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set cityId
     *
     * @param integer $cityId
     * @return Institution
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer 
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     * @return Institution
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;
        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer 
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     * @return Institution
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
     * @return Institution
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
     * @return Institution
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
     * @return Institution
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
     * Add contactDetail
     *
     * @param HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail
     * @return Institution
     */
    public function addContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail)
    {
        $this->contactDetail[] = $contactDetail;
        return $this;
    }

    /**
     * Remove contactDetail
     *
     * @param HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail
     */
    public function removeContactDetail(\HealthCareAbroad\HelperBundle\Entity\ContactDetail $contactDetail)
    {
        $this->contactDetail->removeElement($contactDetail);
    }

    /**
     * Get contactDetail
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getContactDetail()
    {
        return $this->contactDetail;
    }

    /**
     * Add medicalCenters
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $medicalCenters
     * @return Institution
     */
    public function addMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $medicalCenters)
    {
        $this->medicalCenters[] = $medicalCenters;
        return $this;
    }

    /**
     * Remove medicalCenters
     *
     * @param HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $medicalCenters
     */
    public function removeMedicalCenter(\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter $medicalCenters)
    {
        $this->medicalCenters->removeElement($medicalCenters);
    }

    /**
     * Get medicalCenters
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMedicalCenters()
    {
        return $this->medicalCenters;
    }
}