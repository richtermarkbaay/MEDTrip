<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\AdminUserRole;

use Symfony\Component\Security\Core\SecurityContext;

use HealthCareAbroad\UserBundle\Services\ChromediaAccountsUserProvider;

class AdminUserProvider extends ChromediaAccountsUserProvider
{
    /**
     * @param array $accountData
     */
    public function getApplicationUser(array $accountData)
    {
        $user = $this->userService->doctrine->getRepository('UserBundle:AdminUser')->findActiveUserById($accountData['id']);

        if ($user) {
            // populate account data to SiteUser
            $user = $this->userService->hydrateAccountData($user, $accountData);
            $roles = array('ROLE_ADMIN');
            foreach ($user->getAdminUserType()->getAdminUserRoles() as $userRole) {
                // compare bitwise status for active
                if ($userRole->getStatus() & AdminUserRole::STATUS_ACTIVE) {
                    $roles[] = $userRole->getName();
                }
            }
            $user->setRoles($roles);

            //$user->setRoles(array('ROLE_ADMIN', 'IS_AUTHENTICATED_REMEMBERED'));

            return $user;
        }

        return null;
    }

    /**
     *
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::supportsClass()
     */
    public function supportsClass($class)
    {
        return $class === 'HealthCareAbroad\UserBundle\Services\AdminUser';
    }

}