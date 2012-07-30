<?php
namespace HealthCareAbroad\UserBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use ChromediaUtilities\Helpers\Inflector;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\UserService;

class InstitutionUserService extends UserService
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
            $securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'institution_secured_area', array('ROLE_ADMIN'));
            $this->session->set('_security_institution_secured_area',  \serialize($securityToken));
            // $this->get("security.context")->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());
            return true;
        }
        
        return false;
    }
    
    /**
     * Create a institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function create(InstitutionUser $institutionUser)
    {
    	// hash first the password
        $institutionUser->setPassword(SecurityHelper::hash_sha256($institutionUser->getPassword()));
        
        // create user in chromedia global accounts
        if ( $institutionUser = $this->createUser($institutionUser)){
        
            // persist to institution_users table
            $em = $this->doctrine->getEntityManager();
            $em->persist($institutionUser);
            $em->flush();
        
            return $institutionUser;
        }
        
        // something went wrong in creating global account
        return NULL;
    }
    
    /**
     * Update Account of institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function update(InstitutionUser $institutionUser, $accountId)
    {
    	// update user in chromedia global accounts
    	if ( $institutionUser = $this->updateUser($institutionUser, $accountId, FALSE)){
        
    		return TRUE;
    	}
    
    	// something went wrong in creating global account
    	return NULL;
    }
    
    /**
     * Update Account of institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function changePassword(InstitutionUser $institutionUser, $accountId, $password)
    {
    	//set new Password
    	$institutionUser->setPassword($password);

    	// update user in chromedia global accounts
    	if ( $institutionUser = $this->updateUser($institutionUser, $accountId, TRUE)){
    
    		return TRUE;
    	}
    
    	// something went wrong in creating global account
    	return NULL;
    }
    
    /**
     * 
     * @param string $email
     * @param string $password
     * @return InstitutionUser
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
            // find a institution user
            $institutionUser = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);
            
            // populate account data to SiteUser
            $institutionUser = $this->hydrateAccountData($institutionUser, $accountData);
            return $institutionUser;
        }
        
        return null;
    }
    
    /**
     *
     * @param string $email
     * @param string $password
     * @return InstitutionUser
     */
    public function findByIdAndPassword($id, $password)
    {
    	$password = SecurityHelper::hash_sha256($password);
    	$accountData = $this->find(
    			array(
    					'id' => $id,
    					'password' => $password
    			),
    			array('limit' => 1)
    	);
    	
    	if ($accountData) {	
    		return $accountData;
    	}
    	
    	return null;
    }
    	
    
    /**
     * Find a InstitutionUser by accountId
     * 
     * @param int $id
     * @param boolean $activeOnly
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\InstitutionUser>
     */
    public function findById($id, $activeOnly=true)
    {
    	
        // find a institutionUser
        $repository = $this->doctrine->getRepository('UserBundle:InstitutionUser');
        $institutionUser = $activeOnly ? $repository->findActiveUserById($id) : $repository->find($id);
        
        return $institutionUser 
            ? $this->getUser($institutionUser) // find a matching global account for this InstitutionUser 
            : null; // no InstitutionUser found
    }
}