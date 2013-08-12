<?php

namespace HealthCareAbroad\ApiBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

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
    
    /**
     * @var MediaExtension
     */
    private $mediaExtension;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function setMemcache(MemcacheService $v)
    {
        $this->memcache = $v;
    }
    
    public function setMediaExtension(MediaExtension $v)
    {
        $this->mediaExtension = $v;
    }
    
    /**
     * @return \HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension
     */
    public function getMediaExtension()
    {
        return $this->mediaExtension;
    }
    
    /**
     * Build the cover photo source of an institution, if allowed
     * 
     * @param array $institution array data
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionApiService
     */
    public function buildFeaturedMediaSource(&$institution)
    {
        // TODO: provide a more concise list of paying client flags
        if (isset($institution['featuredMedia']) && $institution['payingClient']){
            $institution['featuredMedia']['src'] = $this->mediaExtension->getInstitutionMediaSrc($institution['featuredMedia'], ImageSizes::LARGE_BANNER);
        }
        else {
            $institution['featuredMedia']['src'] = null;
        }
        
        return $this;
    }
    
    /**
     * Build the logo source of an institution, if allowed
     * 
     * @param array array data of $institution
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionApiService
     */
    public function buildLogoSource(&$institution)
    {
        // TODO: provide a more concise list of paying client flags
        if (isset($institution['logo']) && $institution['payingClient']){
            $institution['logo']['src'] = $this->mediaExtension->getInstitutionMediaSrc($institution['logo'], ImageSizes::MEDIUM);
        }
        else {
            $institution['logo']['src'] = null;
        }
        
        return $this;
    }
    
    
    /**
     * Build an array of public data of an institution by slug
     * 
     * @param string $slug
     * @return array $institution data hydrated with HYDRATE_ARRAY
     */
    public function getInstitutionPublicDataBySlug($slug)
    {
        // we need to get the institution id first since this will be the key that we will use for caching
        // we may need to reconsider this, but considering the speed of query and hydration, 
        // this is an acceptable trade off with the consistency of using institution id in memcache 
        $institutionId = $this->doctrine->getRepository('InstitutionBundle:Institution')
            ->getInstitutionIdBySlug($slug);
        
        return $this->getInstitutionPublicDataById($institutionId);
    }
    
    /**
     * Build an array of public data of an institution by slug
     *
     * @param string $slug
     * @return array $institution data hydrated with HYDRATE_ARRAY
     */
    public function getInstitutionPublicDataById($institutionId)
    {
        if (!$institutionId){
            return null;
        }
        $qb = $this->getQueryBuilderForInstitutionPublicProfileData();
        $qb->andWhere('inst.id = :institutionId')
            ->setParameter('institutionId', $institutionId);
        
        $institution = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        
        // build the medical centers data, based on the displayed elements in the medical centers list
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('imc, inst, imc_lg, imc_sp, sp, sp_m')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'imc')
            ->leftJoin('imc.institution', 'inst')
            ->leftJoin('imc.logo', 'imc_lg')
            ->leftJoin('imc.institutionSpecializations', 'imc_sp')
            ->leftJoin('imc_sp.specialization', 'sp')
            ->leftJoin('sp.media', 'sp_m')
            ->where('imc.institution = :institutionId')
            ->setParameter('institutionId', $institutionId)
            // this criteria is a duplicate in the builder for institution
            // but we will replace the entry for institutionMedicalCenters so we have to ensure this
            ->andWhere('imc.status = :imcActiveStatus')
            ->setParameter('imcActiveStatus', InstitutionMedicalCenterStatus::APPROVED)
        ;
        $institution['institutionMedicalCenters'] = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        
        
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
     * Get all global awards by institution id
     * 
     * @param int $institutionId
     * @return array globalAwards hydrated with HYDRATE_ARRAY
     */
    public function getAllGlobalAwards($institutionId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:Institution')
            ->getAllGlobalAwardsByInstitution($institutionId, Query::HYDRATE_ARRAY);
    }
    
    /**
     * 
     * @param int $institutionId
     * @return array offeredServices 
     */
    public function getOfferedServices($institutionId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionProperty')
            ->getAllServicesByInstitution($institutionId, Query::HYDRATE_ARRAY);
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
    private function getQueryBuilderForInstitutionPublicProfileData()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('inst, imc, ct, co, st, icd, fm, lg, gal, gal_m')
            ->from('InstitutionBundle:Institution', 'inst')
            ->innerJoin('inst.institutionMedicalCenters', 'imc', Join::WITH, 'imc.status = :imcActiveStatus')
                ->setParameter('imcActiveStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.contactDetails', 'icd')
            ->leftJoin('inst.featuredMedia', 'fm')
            ->leftJoin('inst.logo', 'lg')
            ->leftJoin('inst.gallery', 'gal')
            ->leftJoin('gal.media', 'gal_m')
            ->where('1=1')
            ->andWhere('inst.status = :activeStatus')
                ->setParameter('activeStatus', InstitutionStatus::getBitValueForApprovedStatus());
        
        return $qb;
    }
    
    /**
     * Build the doctors data of an institution
     * 
     *
     * @param array $institution
     */
    public function buildDoctors(array &$institution)
    {
        $institution['doctors'] = $this->getAllDoctors($institution['id']);
        
        return $this;
    }
    
    /**
     * 
     * 
     * @param array $institution
     */
    public function buildGlobalAwards(array &$institution)
    {
        $institution['globalAwards'] = $this->getAllGlobalAwards($institution['id']);
        
        return $this;
    }
    
    /**
     * 
     * @param array $institution
     */
    public function buildOfferedServices(array &$institution)
    {
        $institution['offeredServices'] = $this->getOfferedServices($institution['id']);
        
        return $this;
    }
}