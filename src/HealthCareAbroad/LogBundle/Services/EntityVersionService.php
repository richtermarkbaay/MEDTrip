<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\LogBundle\Services;

use Doctrine\ORM\EntityRepository;

use HealthCareAbroad\LogBundle\Repository\VersionEntryRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

class EntityVersionService
{
    /**
     * @var Registry
     */
    protected $doctrine;
    
    /**
     * @var VersionEntryRepository
     */
    protected $versionEntryRepository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->versionEntryRepository = $this->doctrine->getRepository('LogBundle:VersionEntry');
    }
    
    public function getObjectVersionEntries($object)
    {
        return $this->versionEntryRepository->getLogEntries($object);
    }
    
    public function buildGenericViewDataFromObject($object)
    {   
        if (\method_exists($object, '__toString')){
            $objectName = $object->__toString();
        }
        elseif (\method_exists($object, 'getName')) {
            $objectName = $object->getName();
        }
        elseif (\method_exists($object, 'getId')) {
            $objectName = '#'.$object->getId();
        }
        else {
            $objectName = null;
        }
        
        return array('id' => $object->getId(), 'name' => $objectName);
    }
    
    public function getVersionEntriesOfInstitutionMedicalCenter($institutionMedicalCenterId)
    {
        // hard coded list of classes that will be part of edit history of a medical center
        $linkedClasses = array(
            '\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter',
            '\HealthCareAbroad\InstitutionBundle\Entity\BusinessHour',
            '\HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty',
            '\HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization',
        );
        
    }
}