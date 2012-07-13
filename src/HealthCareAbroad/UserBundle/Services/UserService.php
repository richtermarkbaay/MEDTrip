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
     * Create a provider user
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\ProviderUser $providerUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function createProviderUser(\HealthCareAbroad\UserBundle\Entity\ProviderUser $providerUser)
    {
        // create user in chromedia global accounts
        if ( $providerUser = $this->createUser($providerUser)){
            
            // persist to provider_users table
            $em = $this->doctrine->getEntityManager();
            $em->persist($providerUser);
            $em->flush();
            
            return $providerUser;
        }
        
        // something went wrong in creating global account
        return NULL;
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
     * Find a user in chromedia global accounts by email and password
     * 
     * @param string $email
     * @param string $password
     */
    public function findByEmailAndPassword($email, $password)
    {
        $searchBy = array('email' => $email, 'password' => $password);
        $option = array('limit' => 1);
        $response = $this->request->post($this->chromediaAccountsUri.'/find', array(
            'searchBy' => \base64_encode(\json_encode($searchBy)),
            'option' => \base64_encode(\json_encode($option))
        ));
        
        if (200 == $response->getStatusCode()) {
            $json_data = \json_decode($response->getBody(true), true);
            $account_data = \count($json_data) ? $json_data[0] : null;
        }
        
        return null;
    }
}