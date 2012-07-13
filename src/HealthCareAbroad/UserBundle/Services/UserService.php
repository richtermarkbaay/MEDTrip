<?php
/**
 * Service class for user related functionalities
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\UserBundle\Services;

class UserService
{
    /**
     * 
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;
    
    /**
     * 
     * @var \HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest
     */
    private $request;
    
    /**
     * 
     * @param \HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest $request
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(\HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest $request,\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->request = $request;   
    }
    
    
    
    /**
     * Create new user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user
     */
    public function createUser(\HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user)
    {
        $this->request->post(null,array(),array());
    }
    
    
    /**
     * Update existing user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user
     */
    public function updateUser(\HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user)
    {
        
    }
}