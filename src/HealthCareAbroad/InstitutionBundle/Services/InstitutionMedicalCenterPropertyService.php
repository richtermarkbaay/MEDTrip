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

    private $propertyRepository;
    
    private static $activePropertyTypes;
    
    private static $institutionMedicalCenterGlobalAwards;
    
    private static $institutionMedicalCenterPropertiesByType = array();
    
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
        $this->_setupAvailablePropertyTypes();

        if (!isset(static::$activePropertyTypes[$propertyTypeName])) {
            throw InstitutionPropertyException::unavailablePropertyType($propertyTypeName);
        }

        return static::$activePropertyTypes[$propertyTypeName];
    }
    
    private function _setupAvailablePropertyTypes()
    {
        // @TODO: Need to check if this still causes some bug.
        if(!static::$activePropertyTypes) {
            $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionPropertyType')->findBy(array('status' => InstitutionPropertyType::STATUS_ACTIVE));
            foreach ($result as $each) {
                static::$activePropertyTypes[$each->getName()] = $each;
            }
        }
    }
    
    /**
     * Get InstitutionMedicalCenterProperties of an institution medical center where property type is global award
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param boolean $loadValuesEagerly
     * @return array InstitutionMedicalCenterProperty
     */
    public function getGlobalAwardPropertiesByInstitutionMedicalCenter(InstitutionMedicalCenter $institutionMedicalCenter, array $options=array())
    {
        if(static::$institutionMedicalCenterGlobalAwards) {
            return static::$institutionMedicalCenterGlobalAwards;
        }
        
        $defaultOptions = array('loadValuesEagerly' => true, 'groupByType' => true);
        $options = \array_merge($defaultOptions, $options);
        $propertyType = $this->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $criteria = array(
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'institutionPropertyType' => $propertyType
        );
        // get the properties
        $properties = $this->propertyRepository->findBy($criteria);
        $returnVal = $properties;

        if ($options['loadValuesEagerly']) {
            $globalAwardIds = array();
            $propertiesByValue = array();
            foreach ($properties as $imp) {
                $globalAwardIds[] = $imp->getValue();
                // store the property with the value as the key
                $propertiesByValue[$imp->getValue()][] = $imp;
            }

            // find global awards with ids equal to the retrieve property values
            $globalAwards = $this->doctrine->getRepository('HelperBundle:GlobalAward')->findByIds($globalAwardIds);
            $returnVal = array();
            // get the property from the stored list
            foreach ($globalAwards as $_award) {
                if (\array_key_exists($_award->getId(), $propertiesByValue) && \is_array($propertiesByValue[$_award->getId()])) {
                    foreach ($propertiesByValue[$_award->getId()] as $imp) {
                        // set the value object to GlobalAward
                        $imp->setValueObject($_award);
                        $returnVal[] = $imp;
                    }
                }
            }
        }

        if ($options['groupByType']) {
            static::$institutionMedicalCenterGlobalAwards = GlobalAwardService::groupGlobalAwardPropertiesByType($returnVal);
        }

        return static::$institutionMedicalCenterGlobalAwards;
    }
    
    public function getInstitutionMedicalCenterByPropertyType(InstitutionMedicalCenter $institutionMedicalCenter, $propertyName)
    {
        if(isset(static::$institutionMedicalCenterPropertiesByType[$propertyName])) {
            return static::$institutionMedicalCenterPropertiesByType[$propertyName];
        }

        $propertyType = $this->getAvailablePropertyType($propertyName);
         
        $criteria = array(
            'institutionMedicalCenter' => $institutionMedicalCenter->getId(),
            'institutionPropertyType' => $propertyType
        );
    
        static::$institutionMedicalCenterPropertiesByType[$propertyName] = $this->propertyRepository->findBy($criteria);

        return static::$institutionMedicalCenterPropertiesByType[$propertyName];
    }
    
    public function addPropertyForInstitutionMedicalCenterByType(Institution $institution, $properties = array(), $propertyTypeName, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $ids = array();
        $propertyType = $this->getAvailablePropertyType($propertyTypeName);
        $currentProperties = $this->propertyRepository->getPropertyValues($propertyType, $institutionMedicalCenter);
        
        foreach ($currentProperties as $property) {
            $ids[$property->getValue()] = $property->getExtraValue();
        }
        $this->removeInstitutionMedicalCenterPropertiesByPropertyType($propertyTypeName, $institutionMedicalCenter);
        $em = $this->doctrine->getManager();
        if(empty($properties)){
            return;
        }
        //TODO: avoid the multiple inserts or check if doctrine will already optimize the queries
        foreach ($properties as $property) {
            $variableName = 'property'.$property;
            $variableName = new InstitutionMedicalCenterProperty();
            $variableName->setInstitution($institution);
            $variableName->setInstitutionMedicalCenter($institutionMedicalCenter);
            $variableName->setInstitutionPropertyType($propertyType);
            if (array_key_exists($property, $ids)) { //check if id exist already
                $variableName->setExtraValue($ids[$property]); // set ExtraValue
            }
            $variableName->setValue($property);
            $em->persist($variableName);
        }
        $em->flush();
        
        return;
    }
    
    public function removeInstitutionMedicalCenterPropertiesByPropertyType($propertyTypeName, InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $propertyType = $this->getAvailablePropertyType($propertyTypeName);
        $currentProperties = $this->propertyRepository->getPropertyValues($propertyType, $institutionMedicalCenter);

        $em = $this->doctrine->getManager();
        foreach ($currentProperties as $property) {
            $em->remove($property);
            $em->flush();
        }
        return;
    }
    public function getCurrentAndSelectedAncillaryServicesByPropertyType(InstitutionMedicalCenter $center, $propertyName, $ancillaryServicesData)
    {
        foreach ($this->getInstitutionMedicalCenterByPropertyType($center, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                            'id' => $_selectedService->getId(),
                            'value' => $_selectedService->getValue(),
            );
            $ancillaryServicesData['selected'][] = $_selectedService->getValue();
        }
        
        return $ancillaryServicesData;
    }
}