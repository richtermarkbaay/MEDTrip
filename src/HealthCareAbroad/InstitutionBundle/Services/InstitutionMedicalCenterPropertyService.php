<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\HelperBundle\Services\GlobalAwardService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionPropertyException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Accessible by service id services.institution_medical_center_property
 * 
 */
class InstitutionMedicalCenterPropertyService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var MemcacheService
     */
    private $memcache;
    
    private $activePropertyTypes;
    private $propertyRepository;
    
    /**
     * @var GlobalAwardService
     */
    private $globalAwardService;
    
    public function __construct(Registry $doctrine, MemcacheService $memcache)
    {
        $this->doctrine = $doctrine;
        $this->memcache = $memcache;
        
        $this->propertyRepository = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty');
        
        //$this->_setupAvailablePropertyTypes();
    }
    
    public function setGlobalAwardService($v)
    {
        $this->globalAwardService = $v;
    }
    
    public  function findById($id)
    {
        return $this->propertyRepository->find($id);
    }
  
    /**
     * Create an instance of InstitutionProperty by property type name
     * 
     * @param string $propertyTypeName
     * @param Institution $institution
     * @return \HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty
     */
    
    public function createInstitutionMedicalCenterPropertyByName($propertyTypeName, Institution $institution=null, InstitutionMedicalCenter $center)
    {
        $propertyType = $this->getAvailablePropertyType($propertyTypeName);
        $property = new InstitutionMedicalCenterProperty();
        $property->setInstitution($institution);
        $property->setInstitutionMedicalCenter($center);
        $property->setInstitutionPropertyType($propertyType);
    
        return $property;
    }
    
    public function removeInstitutionMedicalCenterPropertyByName($propertyTypeName, Institution $institution=null, InstitutionMedicalCenter $center)
    {
        $propertyType = $this->getAvailablePropertyType($propertyTypeName);
        $property = new InstitutionMedicalCenterProperty();
        $property->setInstitution($institution);
        $property->setInstitutionMedicalCenter($center);
        $property->setInstitutionPropertyType($propertyType);
    
        return $property;
    }
    
    public function save(InstitutionMedicalCenterProperty $imcProperty)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($imcProperty);
        $em->flush();
    }
    
    public function createInstitutionMedicalCenterPropertyByServices(InstitutionMedicalCenterProperty $imcProperty)
    {
        $institution = $imcProperty->getInstitution();
        $center = $imcProperty->getInstitutionMedicalCenter();
        $imcType = $imcProperty->getInstitutionPropertyType();
        $imcArray = $imcProperty->getValue();
        if(\is_array($imcArray)) {
            foreach($imcArray as $key => $value)
            {
                $imcProperty = new InstitutionMedicalCenterProperty();
                $imcProperty->setInstitution($institution);
                $imcProperty->setInstitutionPropertyType($imcType);
                $imcProperty->setInstitutionMedicalCenter($center);
                $imcProperty->setValue($value);
                $this->save($imcProperty);
            }
        }
        else {
            $this->save($imcProperty);
        }
    }
    /**
     * @param string $propertyTypeName
     * @return InstitutionPropertyType
     */
    public function getAvailablePropertyType($propertyTypeName)
    {
        static $isLoadedAvailableTypes = false;
        if (!$isLoadedAvailableTypes) {
            $this->_setupAvailablePropertyTypes();
            $isLoadedAvailableTypes = true;
        }
        if (!\array_key_exists($propertyTypeName, $this->activePropertyTypes)) {
            throw InstitutionPropertyException::unavailablePropertyType($propertyTypeName);
        }
        
        return $this->activePropertyTypes[$propertyTypeName];
    }
    
    private function _setupAvailablePropertyTypes()
    {
        $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionPropertyType')->findBy(array('status' => InstitutionPropertyType::STATUS_ACTIVE));
        foreach ($result as $each) {
            $this->activePropertyTypes[$each->getName()] = $each;
        }
    }
    
    /**
     * Get InstitutionMedicalCenterProperties of an institution medical center where property type is global award
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param boolean $loadValuesEagerly
     * @return array InstitutionMedicalCenterProperty
     */
    public function getGlobalAwardPropertiesByInstitutionMedicalCenter(InstitutionMedicalCenter $institutionMedicalCenter, $loadValuesEagerly=true)
    {
        $propertyType = $this->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $criteria = array(
            'institutionMedicalCenter' => $institutionMedicalCenter->getId(),
            'institutionPropertyType' => $propertyType
        );
        // get the properties
        $properties = $this->propertyRepository->findBy($criteria);
        $returnVal = $properties;
        
        if ($loadValuesEagerly) {
            $globalAwardIds = array();
            $propertiesByValue = array();
            foreach ($properties as $imcp) {
                $globalAwardIds[] = $imcp->getValue(); 
                // store the property with the value as the key
                $propertiesByValue[$imcp->getValue()][] = $imcp;
            }
            
            // find global awards with ids equal to the retrieve property values
            $globalAwards = $this->doctrine->getRepository('HelperBundle:GlobalAward')->findByIds($globalAwardIds);

            $returnVal = array();
            // get the property from the stored list
            foreach ($globalAwards as $_award) {
                if (\array_key_exists($_award->getId(), $propertiesByValue) && \is_array($propertiesByValue[$_award->getId()])) {
                    foreach ($propertiesByValue[$_award->getId()] as $imcp) {
                        // set the value object to GlobalAward
                        $imcp->setValueObject($_award);
                        $returnVal[] = $imcp;
                    }
                }
            }
        }
        
        return $returnVal;
    }
    
    public function getInstitutionMedicalCenterByPropertyType(InstitutionMedicalCenter $institutionMedicalCenter, $propertyName)
    {
        $propertyType = $this->getAvailablePropertyType($propertyName);
         
        $criteria = array(
            'institutionMedicalCenter' => $institutionMedicalCenter->getId(),
            'institutionPropertyType' => $propertyType
        );
    
        $properties = $this->propertyRepository->findBy($criteria);
        return $properties;
    }
    
    
    public function addPropertyForInstitutionMedicalCenterByType(Institution $institution, $properties = array(), InstitutionPropertyType $propertyType, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        if(empty($properties)){
            return;
        }
        $em = $this->doctrine->getManager();
    
        //TODO: avoid the multiple inserts or check if doctrine will already optimize the queries
        foreach ($properties as $property) {
            $variableName = 'property'.$property;
            $$variableName = new InstitutionMedicalCenterProperty();
            $$variableName->setInstitution($institution);
            $$variableName->setInstitutionMedicalCenter($institutionMedicalCenter);
            $$variableName->setInstitutionPropertyType($propertyType);
            $$variableName->setValue($property);
    
            $em->persist($$variableName);
        }
        $em->flush();
    }
    
    public function removeInstitutionPropertiesByPropertyType(Institution $institution, InstitutionPropertyType $propertyType, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $currentProperties = $this->propertyRepository->getPropertyValues($institution, $propertyType, $institutionMedicalCenter);

        $em = $this->doctrine->getManager();
        foreach ($currentProperties as $property) {
            $em->remove($property);
            $em->flush();
        }
        return;
    }
}