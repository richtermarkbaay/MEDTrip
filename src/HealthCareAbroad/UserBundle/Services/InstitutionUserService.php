<?php
namespace HealthCareAbroad\UserBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserPasswordToken;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Services\Exception\InvalidInstitutionUserOperationException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use ChromediaUtilities\Helpers\Inflector;
use ChromediaUtilities\Helpers\SecurityHelper;
use HealthCareAbroad\UserBundle\Services\UserService;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;

class InstitutionUserService extends UserService
{
    /**
     * Deprecated please do not use. We will remove this one
     *
     * @param string $email
     * @param password $password
     */
    /**public function login($email, $password)
    {
        $user = $this->findByEmailAndPassword($email, $password);

        if ($user) {

            $userRoles = $user->getInstitutionUserType()->getInstitutionUserRoles();//$user->getInstitutionUserType();

            $roles = array();
            foreach ($userRoles as $userRole) {
                // compare bitwise status for active
                if ($userRole->getStatus() & InstitutionUserRole::STATUS_ACTIVE) {
                    $roles[] = $userRole->getName();
                }
            }

            // add generic role for an admin user
            $roles[] = 'INSTITUTION_USER';

            $securityToken = new UsernamePasswordToken($user->__toString(),$user->getPassword() , 'institution_secured_area', $roles);
            $this->session->set('_security_institution_secured_area',  \serialize($securityToken));
            $this->securityContext->setToken($securityToken);
            $this->session->set('accountId', $user->getAccountId());
            $this->session->set('institutionId', $user->getInstitution()->getId());
            $this->session->set('institutionName', $user->getInstitution()->getName());

            $this->eventDispatcher->dispatch(InstitutionBundleEvents::ON_LOGIN_INSTITUTION_USER, $this->eventFactory->create(InstitutionBundleEvents::ON_LOGIN_INSTITUTION_USER, $user));

            return true;
        }
        return false;
    }**/

    /**
     * @inheritDoc
     */
    public function getUserRolesForSecurityToken(SiteUser $user)
    {
        $roles = array('INSTITUTION_USER');
        foreach ($user->getInstitutionUserType()->getInstitutionUserRoles() as $userRole){
            if ($userRole->getStatus() & InstitutionUserRole::STATUS_ACTIVE) {
                $roles[] = $userRole->getName();
            }
        }

        return $roles;
    }

    public function setSessionVariables(SiteUser $user) {

        $this->session->set('accountId', $user->getAccountId());
        $this->session->set('institutionId', $user->getInstitution()->getId());
        $this->session->set('institutionName', $user->getInstitution()->getName());
        $this->session->set('isSingleCenterInstitution', InstitutionTypes::SINGLE_CENTER == $user->getInstitution()->getType());
        $this->session->set('institutionSignupStepStatus', $user->getInstitution()->getSignupStepStatus());
        $this->session->set('userFirstName', $user->getFirstName());
        $this->session->set('userLastName', $user->getLastName());
        $this->session->set('userEmail', $user->getEmail());

        //$this->eventDispatcher->dispatch(InstitutionBundleEvents::ON_LOGIN_INSTITUTION_USER, $this->eventFactory->create(InstitutionBundleEvents::ON_LOGIN_INSTITUTION_USER, $user));
    }

    /**
     * Create a institution user
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return \HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    //public function create(InstitutionUser $institutionUser)
    public function create(SiteUser $siteUser)
    {
        // removed password hashing here to avoid double hashing
        // $siteUser->setPassword(SecurityHelper::hash_sha256($siteUser->getPassword()));

        // create user in chromedia global accounts
        $siteUser = $this->createUser($siteUser);

        // persist to institution_users table
        $em = $this->doctrine->getEntityManager();
        $em->persist($siteUser);
        $em->flush();

        return $siteUser;
    }

    /**
     * Update Account of institution user. 
     * Password must be encrypted first on the client that uses this service method
     *
     * @param \HealthCareAbroad\UserBundle\Entity\InstitutionUser $institutionUser
     * @return \HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    //public function update(InstitutionUser $institutionUser)
    public function update(SiteUser $siteUser)
    {
        if (!$siteUser->getAccountId()) {
            throw InvalidInstitutionUserOperationException::illegalUpdateWithNoAccountId();
        }
        // removed password hashing here to avoid double hashing when a site user will be updated, without updating the password
        //$siteUser->setPassword(SecurityHelper::hash_sha256($siteUser->getPassword()));

        // update user in chromedia global accounts
        $siteUser = $this->updateUser($siteUser);

        $em = $this->doctrine->getEntityManager();
        $em->persist($siteUser);
        $em->flush();

        return $siteUser;
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @return InstitutionUser
     */
    public function findByEmailAndPassword($email, $password)
    {
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
            $institutionUser = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);

            if ($institutionUser) {
                // populate account data to SiteUser
                $institutionUser = $this->hydrateAccountData($institutionUser, $accountData);

                return $institutionUser;
            }
        }

        return null;
    }

    /**
     *
     * @param string $email
     * @return InstitutionUser
     */
    public function findByEmail($email)
    {
        $accountData = $this->find(
                        array(
                            'email' => $email
                        ),
                        array('limit' => 1)
        );
        if ($accountData) {
            return $accountData['id'];
        }

        return null;
    }

    public function createInstitutionUserPasswordToken($daysofExpiration, $accountId)
    {
        $daysofExpiration = intVal($daysofExpiration);

        //check if expiration days given is 0|less than 0
        if ($daysofExpiration <= 0) {
            $daysofExpiration = 7;
        }

        //generate token
        $generatedToken = SecurityHelper::hash_sha256(date('Ymdhms'));

        //generate expiration date
        $dateNow = new \DateTime('now');
        $expirationDate = $dateNow->modify('+'. $daysofExpiration .' days');

        $passwordToken = new InstitutionUserPasswordToken();
        $passwordToken->setAccountId($accountId);
        $passwordToken->setToken($generatedToken);
        $passwordToken->setExpirationDate($expirationDate);
        $passwordToken->setStatus(SiteUser::STATUS_ACTIVE);

        $em = $this->doctrine->getEntityManager();
        $em->persist($passwordToken);
        $em->flush();
        return $passwordToken;
    }

    public function deleteInstitutionUserPasswordToken(InstitutionUserPasswordToken $token, $institutionUser)
    {
        $em = $this->doctrine->getEntityManager();
        $em->remove($token);
        $em->flush();

        if($institutionUser){
            $this->update($institutionUser);
        }

        return $institutionUser;
    }

    /**
     *
     * @param string $email
     * @param string $password
     * @return InstitutionUser
     */
    public function findByIdAndPassword($id, $password)
    {
        $password = SecurityHelper::hash_sha256($password);
        $accountData = $this->find(
            array(
                'id' => $id,
                'password' => $password
            ),
            array('limit' => 1)
        );

        if ($accountData) {
            //return $accountData;
            // find a institution user
            $institutionUser = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findActiveUserById($accountData['id']);

            // populate account data to SiteUser
            $institutionUser = $this->hydrateAccountData($institutionUser, $accountData);
            return $institutionUser;
        }

        return null;
    }


    /**
     * Find a InstitutionUser by accountId
     *
     * @param int $id
     * @param boolean $activeOnly
     * @return Ambigous <NULL, \HealthCareAbroad\UserBundle\Entity\InstitutionUser>
     */
    public function findById($id, $activeOnly=true)
    {
        // find a institutionUser
        $repository = $this->doctrine->getRepository('UserBundle:InstitutionUser');
        $institutionUser = $activeOnly ? $repository->findActiveUserById($id) : $repository->find($id);
        return $institutionUser
            ? $this->getAccountData($institutionUser) // find a matching global account for this InstitutionUser
            : null; // no InstitutionUser found
    }

    public function getAccountData(SiteUser $siteUser)
    {
        return $this->getUser($siteUser);
    }

    public function findUnexpiredUserPasswordToken($token)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionUserPasswordToken')->findToken($token);
    }
}