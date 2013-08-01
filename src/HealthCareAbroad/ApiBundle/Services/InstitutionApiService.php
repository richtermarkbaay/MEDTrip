<?php

namespace HealthCareAbroad\ApiBundle\Services;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionApiService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var MemcacheService
     */
    private $memcache;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function setMemcache(MemcacheService $v)
    {
        $this->memcache = $v;
    }
    
    /**
     * Build an array of public data of an institution by slug
     * 
     * @param string $slug
     */
    public function buildInstitutionPublicDataBySlug($slug)
    {
        // we need to get the institution id first since this will be the key that we will use for caching
        // we may need to reconsider this, but considering the speed of query and hydration, 
        // this is an acceptable trade off with the consistency of using institution id in memcache 
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('inst.id')
            ->from('InstitutionBundle:Institution', 'inst')
            ->where('inst.slug = :slug')
            ->setParameter('slug', $slug);
        
        $institutionId = (int)$qb->getQuery()->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
        
        return $this->buildInstitutionPublicDataById($institutionId);
    }
    
    /**
     * Build an array of public data of an institution by slug
     *
     * @param string $slug
     */
    public function buildInstitutionPublicDataById($institutionId)
    {
        if (!$institutionId){
            return null;
        }
        
        //TODO: check here for memcache value
        $key = "api.institution_public_data.{$institutionId}";
        $memcachedData = $this->memcache->get($key); 
        if ($memcachedData){
            $institution = $memcachedData;
        }
        else {
            $qb = $this->getQueryBuilderForInstitution();
            $qb->andWhere('inst.id = :institutionId')
                ->setParameter('institutionId', $institutionId);
            
            $institution = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
            if ($institution) {
                // build the doctors data
                $institution['doctors'] = $this->getAllDoctors($institutionId);
            
            
                // build awards data
                $institution['globalAwards'] = $this->doctrine->getRepository('InstitutionBundle:Institution')
                ->getAllGlobalAwardsByInstitution($institutionId, Query::HYDRATE_ARRAY);
            
                //$start = \microtime(true);
                $institution['offeredServices'] = $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')
                ->getAllServicesByInstitution($institutionId, Query::HYDRATE_ARRAY);
            
                //$end = \microtime(true); $diff = $end-$start; echo "{$diff}s"; exit;
            }
            
            // TODO: store to memcache
            $this->memcache->set($key, $institution);
        }
        
        return $institution;
    }
    
    /**
     * Find an institution by slug
     * 
     * @param string $slug
     * @return array institution data
     */
    public function findBySlug($slug)
    {
        $qb = $this->getQueryBuilderForInstitution();
        $qb->andWhere('inst.slug = :slug')
            ->setParameter('slug', $slug); 

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
    
    public function isSingleCenterInstitutionType($type)
    {
        return InstitutionTypes::SINGLE_CENTER == $type;
    }
    
    /**
     * 
     * @param int $institutionId
     * @return array of doctors hydrated with HYDRATE_ARRAY
     */
    public function getAllDoctors($institutionId)
    {
        $qb = $this->doctrine->getRepository('DoctorBundle:Doctor')->getAllDoctorsByInstitution($institutionId);
        
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    /**
     * Get active instituion specializations of an instituion
     * 
     * @param int $institutionId
     * @return array of institution specializations hydrated with HYDRATE_ARRAY
     */
    public function getActiveInstitutionSpecializations($institutionId)
    {
        $specializations = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getActiveSpecializationsByInstitution($institutionId, Query::HYDRATE_ARRAY);
        
        return $specializations;
    }
    
    /**
     * Get a flat array of specializations of an institution
     * 
     * @param int $institutionId
     */
    public function listActiveSpecializations($institutionId)
    {
        $institutionSpecializations = $this->getActiveInstitutionSpecializations($institutionId);
        
        $list = array();
        foreach ($institutionSpecializations as $_each) {
            $specialization = $_each['specialization'];
            $list[$specialization['id']] = $specialization['name'];
        }
        
        return $list;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderForInstitution()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('inst, imc, ct, co, st, icd, fm, lg, gal')
            ->from('InstitutionBundle:Institution', 'inst')
            ->innerJoin('inst.institutionMedicalCenters', 'imc')
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.contactDetails', 'icd')
            ->leftJoin('inst.featuredMedia', 'fm')
            ->leftJoin('inst.logo', 'lg')
            ->leftJoin('inst.gallery', 'gal')
            ->where('1=1')
            ->andWhere('inst.status = :activeStatus')
                ->setParameter('activeStatus', InstitutionStatus::getBitValueForApprovedStatus());
        
        return $qb;
    }
}