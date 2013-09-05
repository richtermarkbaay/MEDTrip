<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionPropertyTypeRepository;

use HealthCareAbroad\HelperBundle\Services\GlobalAwardService;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Exception\InstitutionPropertyException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for InstitutionProperty. Service id services.institution_property
 *
 * @author Allejo Chris G. Velarde
 *
 */
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

    private static $activePropertyTypes;
    
    private static $institutionGlobalAwards = null;

    public function __construct(Registry $doctrine, MemcacheService $memcache)
    {
        $this->doctrine = $doctrine;
        $this->memcache = $memcache;

        //$this->_setupAvailablePropertyTypes();
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

    public function createInstitutionMedicalCenterPropertyByName($propertyTypeName, Institution $institution=null, InstitutionMedicalCenter $center)

    {

        $propertyType = $this->getAvailablePropertyType($propertyTypeName);

        $property = new InstitutionMedicalCenterProperty();

        $property->setInstitution($institution);
        $property->setInstitutionMedicalCenter($center);

        $property->setInstitutionPropertyType($propertyType);



        return $property;

    }

    public function save(InstitutionProperty $institutionProperty)
    {
        $em = $this->doctrine->getEntityManager();

        $em->persist($institutionProperty);

        $em->flush();
    }

    public function createInstitutionPropertyByServices(InstitutionProperty $institutionProperty)
    {
        $institution = $institutionProperty->getInstitution();
        $ipType = $institutionProperty->getInstitutionPropertyType();
        $ipArray = $institutionProperty->getValue();

        if(\is_array($ipArray)) {
            foreach($ipArray as $key => $value)
            {
                $institutionProperty = new InstitutionProperty();
                $institutionProperty->setInstitution($institution);
                $institutionProperty->setInstitutionPropertyType($ipType);
                $institutionProperty->setValue($value);
                $this->save($institutionProperty);
            }
        }
        else {
            $this->save($institutionProperty);
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
        // USING static flag will yield unexpected results when ran in test suites - 
        // @TODO: Need to check if this still causes some bug.
        if (!static::$activePropertyTypes) {
            $result = $this->doctrine->getRepository('InstitutionBundle:InstitutionPropertyType')->findBy(array('status' => InstitutionPropertyType::STATUS_ACTIVE));
            foreach ($result as $each) {
                static::$activePropertyTypes[$each->getName()] = $each;
            }
        }
    }

    /**

     * Layer to Doctrine find by id. Apply caching here.

     *

     * @param int $id

     * @return InstitutionProperty

     */

    public function findById($id)
    {

        return $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')->find($id);
    }

    public function getGlobalAwardPropertiesByInstitution(Institution $institution, array $options=array())
    {
        if(static::$institutionGlobalAwards) {
            return static::$institutionGlobalAwards; 
        }
        
        $defaultOptions = array('loadValuesEagerly' => true, 'groupByType' => true);
        $options = \array_merge($defaultOptions, $options);

        $propertyType = $this->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);

        $criteria = array(
            'institution' => $institution,
            'institutionPropertyType' => $propertyType
        );

        // get the properties

        $properties = $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')->findBy($criteria);
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
            static::$institutionGlobalAwards = GlobalAwardService::groupGlobalAwardPropertiesByType($returnVal);
        }



        return static::$institutionGlobalAwards;

    }
    /**
     * Get Institution Properties by Property Type
     * @author Chaztine Blance
     * @param Institution $institution
     * @param  $propertyName
     * @return properties
     */
    public function getInstitutionByPropertyType(Institution $institution, $propertyName)
    {
        $propertyType = $this->getAvailablePropertyType($propertyName);

        $criteria = array(
            'institution' => $institution,
            'institutionPropertyType' => $propertyType
        );

        $properties = $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')->findBy($criteria);
        return $properties;
    }

    public function addPropertiesForInstitution(Institution $institution, array $services = array(), array $awards = array())
    {
        $this->addAwardsForInstitution($institution, $awards);
        $this->addServicesForInstitution($institution, $services);
    }
    
    public function addAwardsForInstitution(Institution $institution, $awards = array())
    {
        $ids = array();
  
        $propertyType = $this->doctrine->getRepository('InstitutionBundle:InstitutionPropertyType')->find(InstitutionPropertyTypeRepository::GLOBAL_AWARD);
        $currentProperties = $this->getPropertyValues($institution, $propertyType);
        
        foreach ($currentProperties as $property) {

            $ids[$property->getValue()] = $property->getExtraValue();
        }
        $this->removeInstitutionPropertiesByPropertyType($institution, $propertyType);
        $em = $this->doctrine->getManager();
        
        foreach ($awards as $award) {
                $variableName = 'property'.$award;
                $$variableName = new InstitutionProperty();
                $$variableName->setInstitution($institution);
                $$variableName->setInstitutionPropertyType($propertyType);
                
                    if (array_key_exists($award, $ids)) { //check if id exist already
                        
                        $$variableName->setExtraValue($ids[$award]); // set ExtraValue
                    }
                $$variableName->setValue($award);
                $em->persist($$variableName);
        }

        $em->flush();
        
        return;
    }
    
    
    /**

     * Get InstitutionProperty by institution, institution propertype and the value

     *

     * @param Institution $institution

     * @param InstitutionPropertyType $propertyType

     * @param mixed $value

     * @return InstitutionProperty

     */

    public function getPropertyValue(Institution $institution, InstitutionPropertyType $propertyType, $value)

    {

        $dql = "SELECT a FROM InstitutionBundle:InstitutionProperty a WHERE a.institution = :institutionId AND a.institutionPropertyType = :institutionPropertyTypeId AND a.value = :value";

        $result = $this->doctrine->getEntityManager()

        ->createQuery($dql)

        ->setParameter('institutionId', $institution->getId())

        ->setParameter('institutionPropertyTypeId', $propertyType->getId())

        ->setParameter('value', $value)

        ->getOneOrNullResult();

    

        return $result;

    }

    

    /**

     * Get values of institution $institution for property type $propertyType

     *

     * @param Institution $institution

     * @param InstitutionPropertyType $propertyType

     * @return array InstitutionProperty

     */

    public function getPropertyValues(Institution $institution, InstitutionPropertyType $propertyType)

    {

        $dql = "SELECT a FROM InstitutionBundle:InstitutionProperty a WHERE a.institution = :institutionId AND a.institutionPropertyType = :institutionPropertyTypeId";

        $result = $this->doctrine->getEntityManager()

        ->createQuery($dql)

        ->setParameter('institutionId', $institution->getId())

        ->setParameter('institutionPropertyTypeId', $propertyType->getId())

        ->getResult();

        return $result;

    }
    
    /**

     * Check if $institution has a property type value of $value

     *

     * @param Institution $institution

     * @param InstitutionPropertyType $propertyType

     * @param mixed $value

     * @return boolean

     */

    public function hasPropertyValue(Institution $institution, InstitutionPropertyType $propertyType, $value)

    {

        $result = $this->getPropertyValue($institution, $propertyType, $value);

        return !\is_null($result) ;

    }
    
    public function removeInstitutionPropertiesByPropertyType(Institution $institution, InstitutionPropertyType $propertyType)
    {   
        $currentProperties = $this->getPropertyValues($institution, $propertyType);
        
        $em = $this->doctrine->getManager();
         foreach ($currentProperties as $property) {
            $em->remove($property);
            $em->flush();
        }
        return;
    }
    

    public function addServicesForInstitution(Institution $institution, $services = array())
    {
        $propertyType = $this->doctrine->getRepository('InstitutionBundle:InstitutionPropertyType')->find(InstitutionPropertyTypeRepository::ANCILLIARY_SERVICE);

        
        if(empty($services)){
           return;
        }
        $em = $this->doctrine->getManager();

        //TODO: avoid the multiple inserts or check if doctrine will already optimize the queries

        foreach ($services as $service) {
                $variableName = 'property'.$service;

                $$variableName = new InstitutionProperty();

                $$variableName->setInstitution($institution);

                $$variableName->setInstitutionPropertyType($propertyType);

                $$variableName->setValue($service);
                $em->persist($$variableName);

        }
        $em->flush();
    }
    
    public function getUnAssignedInstitutionGlobalAwardsToInstitutionMedicalCenter(Institution $institution, $assignedGlobalAwards)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')->getUnAssignedInstitutionGlobalAwardsToInstitutionMedicalCenter($institution, $assignedGlobalAwards);
    }
}