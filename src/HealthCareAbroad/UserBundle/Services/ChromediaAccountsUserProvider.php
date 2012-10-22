<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Services\UserService;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class ChromediaAccountsUserProvider implements UserProviderInterface
{
    protected $userService;

    abstract function getApplicationUser(array $accountData);

    /**
     *
     * @param UserService $userService
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Note: we are actually using the email for $username
     *
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::loadUserByUsername()
     */
    public function loadUserByUsername($username)
    {
        $accountData = $this->userService->find(array('email'=> $username), array('limit' => 1));

        if ($accountData) {
            if ($applicationUser = $this->getApplicationUser($accountData)) {
                return $applicationUser;
            }
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::refreshUser()
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof SiteUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }
}
