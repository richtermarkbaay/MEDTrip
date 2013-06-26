<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;
use Symfony\Component\Security\Core\SecurityContext;
use HealthCareAbroad\UserBundle\Services\ChromediaAccountsUserProvider;

class InstitutionUserProvider extends ChromediaAccountsUserProvider
{
    /**
     * @var AdminUserService
     */
    private $adminUserService;
    
    public function setAdminUserService(AdminUserService $v)
    {
        $this->adminUserService = $v;
    }
    
    
    /**
     * @param array $accountData
     */
    public function getApplicationUser(array $accountData)
    {
        $user = $this->userService->findById($accountData['id']);
        
        if ($user) {
            // populate account data to SiteUser
            $user = $this->userService->hydrateAccountData($user, $accountData);
            
            // set user roles
            $user->setRoles($this->userService->getUserRolesForSecurityToken($user));

            //TODO: not sure if this is the place to set the session; this
            // shouldn't be part of the user provider's responsibilities
            $this->userService->setSessionVariables($user);

            return $user;
        }
        else {
            // check in the admin user if these credentials belong to internal admin
            $adminUser = $this->adminUserService->findById($accountData['id']);
            
            return $adminUser;
        }

        return null;
    }

    /**
     *
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::supportsClass()
     */
    public function supportsClass($class)
    {
        return $class === 'HealthCareAbroad\UserBundle\Entity\InstitutionUser';
    }

}