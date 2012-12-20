<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

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
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
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
    
    public function hasPropertyValue(InstitutionMedicalCenter $institutionMedicalCenter, InstitutionPropertyType $propertyType, $value)
    {
        $dql = "SELECT a FROM InstitutionBundle:InstitutionMedicalCenterProperty a WHERE a.institutionMedicalCenter = :institutionMedicalCenterId AND a.institutionPropertyType = :institutionPropertyTypeId AND a.value = :value";
        $result = $this->doctrine->getEntityManager()
        ->createQuery($dql)
        ->setParameter('institutionMedicalCenterId', $institutionMedicalCenter->getId())
        ->setParameter('institutionPropertyTypeId', $propertyType->getId())
        ->setParameter('value', $value)
        ->getOneOrNullResult();
        
        return !\is_null($result) ;
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
     * Check if InstitutionMedicalCenter is of DRAFT status
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return boolean
     */
    public function getMedicalCenterServices(InstitutionMedicalCenter $institutionMedicalCenter,Institution $institution)
    {
        $ancilliaryServices = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($institutionMedicalCenter->getId(), $institution->getId());
   
        return $ancilliaryServices;
    }
    
    public function getActiveMedicalCenters(Institution $institution){
        
         $result = $this->doctrine->getRepository('InstitutionBundle:Institution')->getActiveInstitutionMedicalCenters($institution);

         return $result;
    }
}