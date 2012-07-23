<?php
/**
 * Service class for user related functionalities
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\UserBundle\Services;

use ChromediaUtilities\Helpers\Inflector;

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
    
    /**
     * 
     * @var Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;
    
    public function __construct()
    {
            
    }
    
    public function setSession($session)
    {
        $this->session = $session;
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
    protected function createUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
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
    protected function updateUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
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
     * Hydrate account data to SiteUser instance
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUser $user
     * @param array $accountData
     * @return SiteUser
     */
    protected function hydrateAccountData(\HealthCareAbroad\UserBundle\Entity\SiteUser $user, $accountData)
    {
        foreach ($accountData as $key => $v) {
            if ($key != 'id') {
                $setMethod = 'set'.Inflector::toVariable($key);
                $user->{$setMethod}($v);
            }
        }
        return $user;
    }
    
    /**
     * Find user/s in chromedia global accounts based on searchBy options
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
    
    /**
     * Find an account in global chromedia by accountId
     * 
     * @param \HealthCareAbroad\UserBundle\Entity\SiteUser $user
     * @return SiteUser
     */
    public function getUser(\HealthCareAbroad\UserBundle\Entity\SiteUser $user)
    {
        if ($user->getAccountId()){
            $response = $this->request->get($this->chromediaAccountsUri.'/'.$user->getAccountId());
            
            if (200 == $response->getStatusCode()) {
                $accountData = \json_decode($response->getBody(true), true);
                
                return $this->hydrateAccountData($user, $accountData);
            }
        }
        return NULL;
    }
}