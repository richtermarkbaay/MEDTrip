<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\nstitutionUserRole;
use Symfony\Component\Security\Core\SecurityContext;
use HealthCareAbroad\UserBundle\Services\ChromediaAccountsUserProvider;

class InstitutionUserProvider extends ChromediaAccountsUserProvider
{
    /**
     * @param array $accountData
     */
    public function getApplicationUser(array $accountData)
    {
        $user = $this->userService->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);

        if ($user) {
            // populate account data to SiteUser
            $user = $this->userService->hydrateAccountData($user, $accountData);

            $roles = array('ROLE_ADMIN');

            //TODO: why are we looping here?
            foreach ($user->getInstitutionUserType()->getInstitutionUserRole() as $userRole) {
                // compare bitwise status for active
                if ($userRole->getStatus() & InstitutionUserRole::STATUS_ACTIVE) {
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
        return $class === 'HealthCareAbroad\UserBundle\Services\InstitutionUser';
    }

}