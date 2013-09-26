<?php

/**
 * @author Adelbert Silla
 */
namespace HealthCareAbroad\HelperBundle\Services;

use Doctrine\ORM\Query;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Entity\RecentlyApprovedListing;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class RecentlyApprovedListingService
{
    const ACTIVE = 1;


    /** 
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    function getRecentlyApprovedInstitutions()
    {
        $qb = $this->em->createQueryBuilder();
        
        $qb->select('a, b')->from('AdminBundle:RecentlyApprovedListing', 'a')
           ->leftJoin('a.institution', 'b')
           ->where('a.status = :status')
           ->andWhere('a.institutionMedicalCenter IS NULL')
           ->orderBy('a.dateUpdated', 'DESC')
           ->setParameter('status', self::ACTIVE);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    function getRecentlyApprovedInstitutionMedicalCenters()
    {
        $qb = $this->em->createQueryBuilder();
    
        $qb->select('a, b, c')->from('AdminBundle:RecentlyApprovedListing', 'a')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('a.institutionMedicalCenter', 'c')
        ->where('a.status = :status')
        ->andWhere('a.institutionMedicalCenter IS NOT NULL')
        ->orderBy('a.dateUpdated', 'DESC')
        ->setParameter('status', self::ACTIVE);
    
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Added/Updated recentlyApproved institution type listing. Removed when updated from APPROVED to INACTIVE, SUSPENDED or INACTIVE
     * @param Institution $institution
     */
    function updateInstitutionListing(Institution $institution)
    {
        $criteria = array('institution' => $institution->getId(), 'institutionMedicalCenter' => null);
        $recentlyApprovedListing = $this->em->getRepository('AdminBundle:RecentlyApprovedListing')->findOneBy($criteria);

        if($recentlyApprovedListing) {
            
            if($institution->getStatus() == InstitutionStatus::getBitValueForActiveAndApprovedStatus()) {
                $recentlyApprovedListing->setDateUpdated(new \DateTime());
                $this->em->persist($recentlyApprovedListing);
            } else {
                $this->em->remove($recentlyApprovedListing);
            }

            $this->em->flush();

        } else {
            if($institution->getStatus() == InstitutionStatus::getBitValueForActiveAndApprovedStatus()) {
                $recentlyApprovedListingService = new RecentlyApprovedListingService();
                $recentlyApprovedListingService->setEntityManager($this->em);
                $recentlyApprovedListing = new RecentlyApprovedListing();
                $recentlyApprovedListing->setInstitution($institution);
                $recentlyApprovedListing->setInstitutionMedicalCenter(null);
                $recentlyApprovedListing->setDateUpdated(new \DateTime());
                $recentlyApprovedListing->setStatus(1);
            
                $this->em->persist($recentlyApprovedListing);
                $this->em->flush($recentlyApprovedListing);
            }
        }
    }
    
    /**
     * @param InstitutionMedicalCenter $center
     */
    function updateInstitutionMedicalCenterListing(InstitutionMedicalCenter $center)
    {
        $institution = $center->getInstitution();
        $criteria = array('institution' => $institution->getId(), 'institutionMedicalCenter' => $center->getId());
        $recentlyApprovedListing = $this->em->getRepository('AdminBundle:RecentlyApprovedListing')->findOneBy($criteria);

        if($recentlyApprovedListing) {
            if($center->getStatus() == InstitutionMedicalCenterStatus::APPROVED) {
                $recentlyApprovedListing->setDateUpdated(new \DateTime());
                $this->em->persist($recentlyApprovedListing);
            } else {
                $this->em->remove($recentlyApprovedListing);
            }

            $this->em->flush();

        } else {
            if ($center->getStatus() == InstitutionMedicalCenterStatus::APPROVED) {
                $recentlyApprovedListingService = new RecentlyApprovedListingService();
                $recentlyApprovedListingService->setEntityManager($this->em);
                $recentlyApprovedListing = new RecentlyApprovedListing();
                $recentlyApprovedListing->setInstitution($institution);
                $recentlyApprovedListing->setInstitutionMedicalCenter($center);
                $recentlyApprovedListing->setDateUpdated(new \DateTime());
                $recentlyApprovedListing->setStatus(1);

                $this->em->persist($recentlyApprovedListing);
                $this->em->flush($recentlyApprovedListing);
            }            
        } 
    }
}