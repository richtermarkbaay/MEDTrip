<?php
namespace HealthCareAbroad\UserBundle\Services;

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
}