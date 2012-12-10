<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionPropertyException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionPropertyService
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
    public function createInstitutionPropertyByName($propertyTypeName, Institution $institution=null)
    {
        $propertyType = $this->getAvailablePropertyType($propertyTypeName);
        $property = new InstitutionProperty();
        $property->setInstitution($institution);
        $property->setInstitutionPropertyType($propertyType);
        
        return $property;
    }
    
    public function save(InstitutionProperty $institutionProperty)
    {
        $institution = $institutionProperty->getInstitution();
        $ipType = $institutionProperty->getInstitutionPropertyType();
        $ipArray = $institutionProperty->getValue();
        $em = $this->doctrine->getEntityManager();
        if(\is_object($ipArray)) {
            foreach($ipArray as $key => $value)
            {
                $institutionProperty = new InstitutionProperty();
                $institutionProperty->setInstitution($institution);
                $institutionProperty->setInstitutionPropertyType($ipType);
                $institutionProperty->setValue($value->getId());
                $this->createInstitutionProperty($instituionProperty);
            }
        }
        else {
            $this->createInstitutionProperty($instituionProperty);
        }
    }
    
    public function createInstitutionProperty($instituionProperty)
    {
        $em->persist($institutionProperty);
        $em->flush();
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