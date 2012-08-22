<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\UserBundle\Services\Exception\InvalidInstitutionUserOperationException;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use ChromediaUtilities\Helpers\Inflector;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\UserService;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;
class InstitutionUserService extends UserService
{
    /**
     * 
     * @param string $email
     * @param password $password
     */
    public function login($email, $password)
    {
    	$user = $this->findByEmailAndPassword($email, $password);
        
        if ($user) {
        	$userRoles = $user->getInstitutionUserType();
        	$roles = array();
        	$roles[] = $userRoles->getName();
        	
        	// add generic role for an admin user
        	$roles[] = 'ROLE_ADMIN';

        	$securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'institution_secured_area', $roles);
            $this->session->set('_security_institution_secured_area',  \serialize($securityToken));
            // $this->get("security.context")->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());
            $this->session->set('institutionId', $user->getInstitution()->getId());
            $this->session->set('institutionName', $user->getInstitution()->getName());
            return true;
        }
        return false;
    }
    
    /**
     * Create a institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return \HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    public function create(InstitutionUser $institutionUser)
    {
    	// hash first the password
        $institutionUser->setPassword(SecurityHelper::hash_sha256($institutionUser->getPassword()));
        
        // create user in chromedia global accounts
        $institutionUser = $this->createUser($institutionUser);
                
        // persist to institution_users table
        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionUser);
        $em->flush();
        
        return $institutionUser;
    }
    
    /**
     * Update Account of institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return \HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    public function update(InstitutionUser $institutionUser)
    {
        if (!$institutionUser->getAccountId()) {
            throw InvalidInstitutionUserOperationException::illegalUpdateWithNoAccountId();
        }
        
    	// update user in chromedia global accounts
        $institutionUser = $this->updateUser($institutionUser);
    	return $institutionUser;
    }
    
    /**
     * 
     * @param string $email
     * @param string $password
     * @return InstitutionUser
     */
    public function findByEmailAndPassword($email, $password)
    {
        $password = SecurityHelper::hash_sha256($password);
        $accountData = $this->find(
            array(
                'email' => $email, 
                'password' => $password
            ), 
            array('limit' => 1)
        );
        if ($accountData) {
            // find an institution user
            $institutionUser = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);
            
            if ($institutionUser) {
                // populate account data to SiteUser
                $institutionUser = $this->hydrateAccountData($institutionUser, $accountData);
                
                return $institutionUser;
            }
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
    		//return $accountData;
    	    // find a institution user
    	    $institutionUser = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);
    	    
    	    // populate account data to SiteUser
    	    $institutionUser = $this->hydrateAccountData($institutionUser, $accountData);
    	    return $institutionUser;
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
            ? $this->getAccountData($institutionUser) // find a matching global account for this InstitutionUser 
            : null; // no InstitutionUser found
    }
    
    public function getAccountData(InstitutionUser $institutionUser)
    {
        return $this->getUser($institutionUser);
    }
}