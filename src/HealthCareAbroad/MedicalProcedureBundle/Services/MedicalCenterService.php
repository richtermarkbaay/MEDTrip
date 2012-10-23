<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Services;

/**
 * Medical center service class. Accessible through 'services.medical_center'.
 * 
 * @author Allejo Chris G. Velarde
 */
use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

class MedicalCenterService
{
    /**
     * @var MemcacheService
     */
    private $memcache;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setMemcache(MemcacheService $memcache)
    {
        $this->memcache = $memcache;
    }
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * Get MedicalCenters with status ACTIVE
     * 
     * @return array MedicalCenter
     */
    public function getAllActiveMedicalCenters()
    {
        // check in cache
        $key = 'MedicalProcedureBundle:MedicalCenterService:getAllActiveMedicalCenters';
        $result = $this->memcache->get($key);
        if (!$result) {
            $result = $this->doctrine->getRepository('MedicalProcedureBundle:MedicalCenter')
                ->getQueryBuilderForActiveMedicalCenters()
                ->getQuery()->getResult();
            
            // cache this result
            $this->memcache->set($key, $result);
        }
        
        return $result;
    }
}