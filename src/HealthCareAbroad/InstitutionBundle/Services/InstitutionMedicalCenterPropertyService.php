<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionPropertyException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

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
    
    public function __construct(Registry $doctrine, MemcacheService $memcache)
    {
        $this->doctrine = $doctrine;
        $this->memcache = $memcache;
        
        $this->_setupAvailablePropertyTypes();
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
}