<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * service id: services.helper.page_meta_configuration
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class PageMetaConfigurationService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function findOneByUrl($url)
    {
        return $this->doctrine->getRepository('HelperBundle:PageMetaConfiguration')->findOneByUrl($url);   
    }
    
    public function save(PageMetaConfiguration $pageMetaConfiguration)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($pageMetaConfiguration);
        $em->flush();
    }
}