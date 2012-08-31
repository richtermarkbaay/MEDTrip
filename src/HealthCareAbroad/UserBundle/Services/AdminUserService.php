<?php
namespace HealthCareAbroad\UserBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ChromediaUtilities\Helpers\SecurityHelper;
use ChromediaUtilities\Helpers\Inflector;

use HealthCareAbroad\UserBundle\Entity\AdminUserRole;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Services\Exception\InvalidInstitutionUserOperationException;
use HealthCareAbroad\UserBundle\Entity\AdminUser;

class AdminUserService extends UserService
{
    public function getAccountData(AdminUser $user)
    {
        return $this->getUser($user);
    }
    
    public function getActiveUsers()
    {
        $users = $this->doctrine->getRepository('UserBundle:AdminUser')->getActiveUsers();
        $returnVal = array();
        foreach ($users as $user) {
            $returnVal[] = $this->getAccountData($user);
        }
        
        return $returnVal;
    }
    
    public function login($email, $password)
    {
        $user = $this->findByEmailAndPassword($email, $password);
        if ($user) {
            $userRoles = $user->getAdminUserType()->getAdminUserRoles();
            $roles = array();
            foreach ($userRoles as $userRole) {
                // compare bitwise status for active
                if ($userRole->getStatus() & AdminUserRole::STATUS_ACTIVE) {
                    $roles[] = $userRole->getName();
                }
            }
            
            // add generic role for an admin user
            $roles[] = 'ROLE_ADMIN';
            
            $securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'admin_secured_area', $roles);
            $this->session->set('_security_admin_secured_area',  \serialize($securityToken));
            $this->securityContext->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Create an AdminUser
     * 
     * @param AdminUser $user
     * @return HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    public function create(AdminUser $user)
    {
        // create user in chromedia global accounts
        $user->setPassword(SecurityHelper::hash_sha256($user->getPassword()));// hash the password
        $user = $this->createUser($user);
        
        // persist to admin_users table
        $em = $this->doctrine->getEntityManager();
        $em->persist($user);
        $em->flush();
        
        return $user;
    }
    
    
    public function update(AdminUser $user)
    {
    	//update data in chromedia global accounts
    	if (!$user->getAccountId()) {
            throw InvalidInstitutionUserOperationException::illegalUpdateWithNoAccountId();
        }
        
    	// update user in chromedia global accounts
        $user = $this->updateUser($user);
    	return $user;
    }
    /**
     * Find an AdminUser based on email and password
     * 
     * @param string $email
     * @param string $password
     * @return HealthCareAbroad\UserBundle\Entity\AdminUser
     */
    public function findByEmailAndPassword($email, $password)
    {
        // find the account in the global chromedia accounts
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
            $adminUser = $this->doctrine->getRepository('UserBundle:AdminUser')->findActiveUserById($accountData['id']);
        
            if ($adminUser) {
                // populate account data to SiteUser
                $adminUser = $this->hydrateAccountData($adminUser, $accountData);
        
                return $adminUser;
            }
        }
        return null;
    }
    
    /**
     * Find a AdminUser by accountId
     *
     * @param int $id
     * @param boolean $activeOnly
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\AdminUser>
     */
    public function findById($id, $activeOnly=true)
    {
    	 
    	// find a adminUser
    	$repository = $this->doctrine->getRepository('UserBundle:AdminUser');
    	$adminUser = $activeOnly ? $repository->findActiveUserById($id) : $repository->find($id);
    
    	return $adminUser
    	? $this->getAccountData($adminUser) // find a matching global account for this AdminUser
    	: null; // no AdminUser found
    }
    
}