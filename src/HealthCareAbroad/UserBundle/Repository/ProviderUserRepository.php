<?php
namespace HealthCareAbroad\UserBundle\Repository;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Doctrine\ORM\EntityRepository;

/**
 * ProviderUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProviderUserRepository2 extends EntityRepository
{
    /**
     * 
     * @param int $accountId
     * @return HealthCareAbroad\UserBundle\Entity\ProviderUser
     */
    public function findActiveUserById($accountId)
    {
        return $this->findOneBy(array('accountId' => $accountId, 'status' => SiteUser::STATUS_ACTIVE));
    }
}