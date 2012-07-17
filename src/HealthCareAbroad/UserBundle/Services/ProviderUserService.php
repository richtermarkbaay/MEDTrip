<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use ChromediaUtilities\Helpers\Inflector;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\UserService;

class ProviderUserService extends UserService
{
    /**
     * Create a provider user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\ProviderUser $providerUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function create(ProviderUser $providerUser)
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
     * 
     * @param string $email
     * @param string $password
     */
    public function findByEmailAndPassword($email, $password)
    {
        $accountData = $this->find(
            array(
                'email' => $email, 
                'password' => $password
            ), 
            array('limit' => 1)
        );
        
        if ($accountData) {
            // find a provider user
            $providerUser = $this->doctrine->getRepository('UserBundle:ProviderUser')->getActiveUserByAccountId($accountData['id']);
            
            // populate account data to SiteUser
            foreach ($accountData as $key => $v) {
                if ($key != 'id') {
                    $setMethod = 'set'.Inflector::toVariable($key);
                    $providerUser->{$setMethod}($v);
                }
            }
            
            
            return $providerUser;
        }
        
        return null;
    }
}