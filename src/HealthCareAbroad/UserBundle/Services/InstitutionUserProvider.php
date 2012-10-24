<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;
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

            $roles = array('INSTITUTION_USER');

            //TODO: why are we looping here?
            foreach ($user->getInstitutionUserType()->getInstitutionUserRole() as $userRole) {
                // compare bitwise status for active
                if ($userRole->getStatus() & InstitutionUserRole::STATUS_ACTIVE) {
                    $roles[] = $userRole->getName();
                }
            }
            $user->setRoles($roles);

            //TODO: not sure if this is the place to set the session; this
            // shouldn't be part of the user provider's responsibilities
            $this->userService->setSessionVariables($user);

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
        return $class === 'HealthCareAbroad\UserBundle\Entity\InstitutionUser';
    }

}