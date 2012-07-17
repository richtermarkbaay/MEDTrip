<?php
/**
 * Service class for user related functionalities
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

class UserService
{
    /**
     * 
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
    
    /**
     * 
     * @var \HealthCareAbroad\HelperBundle\Services\ChromediaGlobalRequest
     */
    protected $request;
    
    protected $chromediaAccountsUri;
    
    
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
     * Create new user in the global chromedia accounts
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUser $user
     * @return NULL | SiteUser
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
        if (200 == $response->getStatusCode()) {
            $account_data = \json_decode($response->getBody(true),true);
            $user->setAccountId($account_data['id']);
            return $user;
        }
        else {
            return null;
        }
    }
    
    
    /**
     * Update existing user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUserInterface $user
     */
    public function updateUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
    {
        
    }
    
    /**
     * Find a user in chromedia global accounts
     * 
     * @param array $searchBy
     * @param array $options
     * @return array
     */
    public function find($searchBy, $options)
    {
        $response = $this->request->post($this->chromediaAccountsUri.'/find', array(
            'searchBy' => \base64_encode(\json_encode($searchBy)),
            'option' => \base64_encode(\json_encode($options))
        ));
        
        if (200 == $response->getStatusCode()) {
            $json_data = \json_decode($response->getBody(true), true);
            return $json_data;
        }
        
        return null;
    }
}