<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use ChromediaUtilities\Helpers\SecurityHelper;
use ChromediaUtilities\Helpers\Inflector;

use HealthCareAbroad\UserBundle\Entity\AdminUserRole;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Services\Exception\InvalidInstitutionUserOperationException;
use HealthCareAbroad\UserBundle\Entity\AdminUser;

class AdminUserService extends UserService
{
    public function getAccountData(SiteUser $siteUser)
    {
        return $this->getUser($siteUser);
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

    /**
     * This is subject for removal.
     */
    /*public function login($email, $password)
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
//             $this->securityContext->setToken($securityToken);
            $this->container->get('security.context')->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());

            // dispatch event
            $this->eventDispatcher->dispatch(AdminBundleEvents::ON_LOGIN_ADMIN_USER, $this->eventFactory->create(AdminBundleEvents::ON_LOGIN_ADMIN_USER, $user));

            return true;
        }

        return false;
    }*/
    
    /**
     * @inheritDoc
     */
    public function getUserRolesForSecurityToken(SiteUser $user)
    {
        $roles = array('ROLE_ADMIN');
        foreach ($user->getAdminUserType()->getAdminUserRoles() as $userRole) {
            // compare bitwise status for active
            if ($userRole->getStatus() & AdminUserRole::STATUS_ACTIVE) {
                $roles[] = $userRole->getName();
            }
        }
        
        return $roles;
    }

    /**
     * @param SiteUser $user
     */
    public function setSessionVariables(SiteUser $user) {
        $this->session->set('accountId', $user->getAccountId());

        // dispatch event
        $this->eventDispatcher->dispatch(AdminBundleEvents::ON_LOGIN_ADMIN_USER, $this->eventFactory->create(AdminBundleEvents::ON_LOGIN_ADMIN_USER, $user));
    }

    /**
     * Create an AdminUser
     *
     * @param AdminUser $user
     * @return HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    public function create(SiteUser $siteUser)
    {
        // create user in chromedia global accounts
        $siteUser->setPassword(SecurityHelper::hash_sha256($siteUser->getPassword()));// hash the password
        $siteUser->setStatus(SiteUser::STATUS_ACTIVE);
        $siteUser = $this->createUser($siteUser);

        // persist to admin_users table
        $em = $this->doctrine->getEntityManager();
        $em->persist($siteUser);
        $em->flush();

        return $siteUser;
    }


    public function update(SiteUser $siteUser)
    {
        //update data in chromedia global accounts
        if (!$siteUser->getAccountId()) {
            return null;
        }

        $siteUser = $this->updateUser($siteUser);
        return $siteUser;
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