<?php
namespace HealthCareAbroad\UserBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use ChromediaUtilities\Helpers\Inflector;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\UserService;

class ProviderUserService extends UserService
{
    /**
     * 
     * @param string $email
     * @param password $password
     */
    public function login($email, $password)
    {
    	$password = SecurityHelper::hash_sha256($password);
        $user = $this->findByEmailAndPassword($email, $password);
        
        if ($user) {
            $securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'provider_secured_area', array('ROLE_ADMIN'));
            $this->session->set('_security_provider_secured_area',  \serialize($securityToken));
            // $this->get("security.context")->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a provider user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\ProviderUser $providerUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function create(ProviderUser $providerUser)
    {
        // hash first the password
        $providerUser->setPassword(SecurityHelper::hash_sha256($providerUser->getPassword()));
        
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
     * Update Account of provider user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\ProviderUser $providerUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function update(ProviderUser $providerUser, $accountId)
    {
    	//var_dump($providerUser);exit;aa
    	// update user in chromedia global accounts
    	if ( $providerUser = $this->updateUser($providerUser)){
        
    		return new Response ('$providerUser');
    	}
    
    	// something went wrong in creating global account
    	return NULL;
    }
    
    /**
     * 
     * @param string $email
     * @param string $password
     * @return ProviderUser
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
            $providerUser = $this->doctrine->getRepository('UserBundle:ProviderUser')->findActiveUserById($accountData['id']);
            
            // populate account data to SiteUser
            $providerUser = $this->hydrateAccountData($providerUser, $accountData);
            echo $providerUser;exit;
            return $providerUser;
        }
        
        return null;
    }
    
    /**
     * Find a ProviderUser by accountId
     * 
     * @param int $id
     * @param boolean $activeOnly
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\ProviderUser>
     */
    public function findById($id, $activeOnly=true)
    {
    	
        // find a providerUser
        $repository = $this->doctrine->getRepository('UserBundle:ProviderUser');
        $providerUser = $activeOnly ? $repository->findActiveUserById($id) : $repository->find($id);
        
        return $providerUser 
            ? $this->getUser($providerUser) // find a matching global account for this ProviderUser 
            : null; // no providerUser found
    }
}