<?php
namespace HealthCareAbroad\UserBundle\Services;

use ChromediaUtilities\Helpers\Inflector;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\UserService;

class ProviderUserService extends UserService
{
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