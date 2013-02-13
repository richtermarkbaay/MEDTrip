<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use HealthCareAbroad\UserBundle\Services\Exception\FailedAccountRequestException;

use Guzzle\Http\Exception\CurlException;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Services\UserService;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

abstract class ChromediaAccountsUserProvider implements UserProviderInterface
{
    protected $userService;
    protected $supportedClass;

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
        try {
            $chromediaAccount = $this->userService->find(array('email'=> $username), array('limit' => 1));
        } catch (CurlException $e) {
            throw new FailedAccountRequestException('Authentication service unavailable. Please try again later.');
        }

        if ($chromediaAccount) {
            if ($applicationUser = $this->getApplicationUser($chromediaAccount)) {
                return $applicationUser;
            }
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    /**
     * Note: that this method employs a workaround for https://github.com/symfony/symfony/issues/4498
     *
     * Currently the way the security bundle handles multiple providers is this:
     * After user logs in, the ContextListener attempts to validate user token
     * and refresh user by iterating over every user provider and calling its
     * refreshUser method. If current user provider does not support the user
     * class it throws UnsupportedUserException and ContextListener continues
     * to the next user provider, but if it does and can't actually find the
     * user it throws UsernameNotFoundException which interrupts the iteration
     * and returns null essentially forcing the user to log out;
     *
     * or in other words, when ContextListener refreshes a user after login it
     * checks ALL user providers, not just the ones specific to the firewall,
     * so it tries to load an admin user from the members user provider, fails,
     * and logs out the admin.
     *
     * This is the simplest and certainly not the most robust solution that I
     * can find without intruding too much into symfony internals.
     *
     * Important!!! It's possible that later versions of symfony will break this
     * workaround and an offical and more proper way of retrieving a user from
     * the set of user providers will be available.
     *
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::refreshUser()
     */
    public function refreshUser(UserInterface $user)
    {
        //this is what we can resonably expect to be symfony's default behavior
        if (!$user instanceof SiteUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        //this may be implemented internally by symfony in later versions albeit
        //not in this specific form. see ticket above
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        //TODO: use some strategy to avoid calling the chromedia account service
        // for every request
        
        // quick fix for now just to help admin users with slow load time
        // FIXME 
        if ($user instanceof AdminUser) {
            return $user;
        }
        else  {
            return $user;
            //return $this->loadUserByUsername($user->getUsername());
        }
    }

}
