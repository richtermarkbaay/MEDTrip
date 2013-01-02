<?php

namespace HealthCareAbroad\HelperBundle\Services;

/**
 * Service class for AncillaryService. Accessible through service id services.helper.ancillary_service
 * @author Allejo Chris G. Velarde
 *
 */
use HealthCareAbroad\AdminBundle\Repository\OfferedServiceRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

class AncillaryServicesService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var OfferedServiceRepository
     */
    private $repository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('AdminBundle:OfferedService');
    }
    
    public function getActiveAncillaryServices()
    {
        $qb = $this->repository->getBuilderForOfferedServices();
        
        return  $qb->getQuery()->execute();
    }
}