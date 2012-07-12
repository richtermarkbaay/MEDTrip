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
    
    private $chromediaAccountsUri;
    
    
    public function __construct()
    {
            
    }
    
    /**
     * 
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * 
     * @param \HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest $request
     */
    public function setChromediaRequest(\HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest $request)
    {
        $this->request = $request;
    }
    
    public function setChromediaAccountsUri($uri)
    {
        $this->chromediaAccountsUri = $uri;
    }
    
    /**
     * Create new user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user
     */
    public function createUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
    {
        
        $form_data = array(
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'middle_name' => $user->getMiddleName()
        );
        
        $response = $this->request->post($this->chromediaAccountsUri,array('data' => \base64_encode(\json_encode($form_data))));
        
        echo $response;
    }
    
    
    /**
     * Update existing user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user
     */
    public function updateUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
    {
        
    }
}