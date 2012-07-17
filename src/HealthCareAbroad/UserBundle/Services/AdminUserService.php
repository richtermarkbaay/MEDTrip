<?php
namespace HealthCareAbroad\UserBundle\Services;

use ChromediaUtilities\Helpers\Inflector;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

class AdminUserService extends UserService
{
    /**
     * Create an AdminUser
     * 
     * @param AdminUser $user
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\SiteUser>|NULL
     */
    public function create(AdminUser $user)
    {
        // create user in chromedia global accounts
        if ( $user = $this->createUser($user)){
        
            // persist to admin_users table
            $em = $this->doctrine->getEntityManager();
            $em->persist($user);
            $em->flush();
        
            return $user;
        }
        
        // something went wrong in creating global account
        return NULL;
    }
    
    /**
     * Find an AdminUser based on email and password
     * 
     * @param string $email
     * @param string $password
     * @return NULL|HealthCareAbroad\UserBundle\Entity\AdminUser
     */
    public function findByEmailAndPassword($email, $password)
    {
        // find the account in the global chromedia accounts
        $accountData = $this->find(
            array(
                'email' => $email,
                'password' => $password
            ),
            array('limit' => 1)
        );
        
        if (!$accountData) {
            return null;
        }
        
        // get the active admin user for this account
        $accountId = \array_key_exists('id', $accountData) ? $accountData['id'] : 0;
        $adminUser = $this->doctrine->getRepository('UserBundle:AdminUser')->findOneBy(array('accountId' => $accountId, 'status' => SiteUser::STATUS_ACTIVE));
        if (!$adminUser) {
            return null;
        }
        
        // populate account data to SiteUser
        foreach ($accountData as $key => $v) {
            if ($key != 'id') {
                $setMethod = 'set'.Inflector::toVariable($key);
                $adminUser->{$setMethod}($v);
            }
        }
        
        return $adminUser;
    }
}