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
}