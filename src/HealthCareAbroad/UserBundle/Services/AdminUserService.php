<?php
namespace HealthCareAbroad\UserBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ChromediaUtilities\Helpers\SecurityHelper;

use ChromediaUtilities\Helpers\Inflector;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

class AdminUserService extends UserService
{
    public function login($email, $password)
    {
        $user = $this->findByEmailAndPassword($email, $password);
        if ($user) {
            $securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'admin_secured_area', array('ROLE_ADMIN'));
            $this->session->set('_security_admin_secured_area',  \serialize($securityToken));
            //$this->get("security.context")->setToken($securityToken);
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
}