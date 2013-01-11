<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for InstitutionMedicalCenter. Accessible by services.institution_medical_center service id
 * 
 * @author Allejo Chris G. Velarde
 */
class InstitutionMedicalCenterService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var InstitutionMedicalCenterPropertyService
     */
    private $institutionMedicalCenterPropertyService;
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function setInstitutionMedicalCenterPropertyService(InstitutionMedicalCenterPropertyService $service)
    {
        $this->institutionMedicalCenterPropertyService = $service;
    }
    
    /**
     * Get values of medical center $institutionMedicalCenter for property type $propertyType 
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @return array InstitutionMedicalCenterProperty
     */
    public function getPropertyValues(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId";
        $result = $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->getResult();
        
        return $result;
    }
    
    /**
     * Check if $institutionMedicalCenter has a property type value of $value
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @param mixed $value
     * @return boolean
     */
    public function hasPropertyValue(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType, $value)
    {
        $result = $this->getPropertyValue($institutionMedicalCenter, $propertyType, $value);   
        
        return !\is_null($result) ;
    }
    
    /**
     * Get InstitutionMedicalCenterProperty by institution medical center, institution propertype and the value
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     * @param mixed $value
     * @return InstitutionMedicalCenterProperty
     */
    public function getPropertyValue(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType, $value)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId AND a.value = :value";
        $result = $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->setParameter('value', $value)
            ->getOneOrNullResult();
        
        return $result;
    }
    
    /**
     * Delete the values for $propertyType of $institutionMedicalCenter
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param InstitutionPropertyType $propertyType
     */
    public function clearPropertyValues(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType)
    {
        $dql = "DELETE FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId";
        $this->doctrine->getEntityManager()
            ->createQuery($dql)
            ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
            ->setParameter('institutionPropertyTypeId', $propertyType->getId())
            ->execute();
    }
    
    /**
     * Layer to Doctrine find by id. Apply caching here.
     * 
     * @param int $id
     * @return InstitutionMedicalCenter
     */
    public function findById($id)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($id);
    }
    
    /**
     * Save InstitutionMedicalCenter to database
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return InstitutionMedicalCenter
     */
    public function save(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionMedicalCenter);
        $em->flush();
        
        return $institutionMedicalCenter;
    }
    
    public function setInstitutionStatusActive(Institution $institution)
    {
        $institution->setStatus(Institution::ACTIVE);
        $em = $this->doctrine->getEntityManager();
        $em->persist($institution);
        $em->flush();
    }
    /**
     * Save an InstitutionMedicalCenter as DRAFT
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function saveAsDraft(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::DRAFT);
        
        return $this->save($institutionMedicalCenter);
    }
    
    /**
     * Check if InstitutionMedicalCenter is of DRAFT status
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return boolean
     */
    public function isDraft(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $institutionMedicalCenter->getStatus() == InstitutionMedicalCenterStatus::DRAFT;
    }
    
    
    /**
     * Get ancillary services of a medical center
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return boolean
     */
    public function getMedicalCenterServices(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $ancilliaryServices = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($institutionMedicalCenter);
   
        return $ancilliaryServices;
    }
    
    public function getMedicalCenterGlobalAwards(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllGlobalAwardsByInstitutionMedicalCenter($institutionMedicalCenter);
    }
    
    public function getActiveMedicalCenters(Institution $institution)
    {
        
         $result = $this->doctrine->getRepository('InstitutionBundle:Institution')->getActiveInstitutionMedicalCenters($institution);

         return $result;
    }
    

    public function getAvailableTreatmentsByInstitutionSpecialization(InstitutionSpecialization $institutionSpecialization)
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getAvailableTreatments($institutionSpecialization);
        
        return $result;
    }

    /**
     * Check if specialist exist
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return array InstitutionMedicalCenterProperty
     */
    public function hasSpecialist(InstitutionMedicalCenter $institutionMedicalCenter, $doctor)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a 
        LEFT JOIN a.doctors b
        WHERE a.id = :institutionMedicalCenterId AND b.id = :doctorId";
        $result = $this->doctrine->getEntityManager()
        ->createQuery($dql)
        ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
        ->setParameter('doctorId', $doctor)
        ->getResult();
    
        return $result;
    }
    

}