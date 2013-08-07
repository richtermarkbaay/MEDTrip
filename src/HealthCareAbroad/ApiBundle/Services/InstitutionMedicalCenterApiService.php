<?php

namespace HealthCareAbroad\ApiBundle\Services;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionMedicalCenterApiService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var MediaExtension
     */
    private $mediaExtensionService;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function setMediaExtension(MediaExtension $v)
    {
        $this->mediaExtensionService = $v;
    }
    
    /**
     * 
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildLogoSource(&$institutionMedicalCenter)
    {
        $canDisplayImcLogo = $institutionMedicalCenter['institution']['payingClient'];
        
        // client is allowed to display logo, and there is a logo
        if ($canDisplayImcLogo && $institutionMedicalCenter['logo']) {
            $institutionMedicalCenter['logo']['src'] = $this->mediaExtensionService->getInstitutionMediaSrc($institutionMedicalCenter['logo'], ImageSizes::MEDIUM);
        }
        else {
            // not allowed to display logo, or clinic has no logo
            // we get the logo of the first specialization
            $firstSpecialization = \count($institutionMedicalCenter['institutionSpecializations'])
            ? (isset($institutionMedicalCenter['institutionSpecializations'][0]['specialization']) ? $institutionMedicalCenter['institutionSpecializations'][0]['specialization'] : null)
            : null;
        
            // first specialization has a media
            if ($firstSpecialization && $firstSpecialization['media']){
                $institutionMedicalCenter['logo']['src'] = $this->mediaExtensionService->getSpecializationMediaSrc($firstSpecialization['media'], ImageSizes::SPECIALIZATION_DEFAULT_LOGO);
            }
        }
        
        return $this;
    }
    
    public function getInstitutionMedicalCenterPublicDataById($institutionMedicalCenterId)
    {
        $qb = $this->getQueryBuilderForFullInstitutionMedicalCenterProfile();
        $qb->andWhere('imc.id = :id')
            ->setParameter('id', $institutionMedicalCenterId);
        
        $institutionMedicalCenter = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        
        return $institutionMedicalCenter;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildDoctors(&$institutionMedicalCenter)
    {
        $institutionMedicalCenter['doctors'] = $this->getDoctorsByInstitutionMedicalCenterId($institutionMedicalCenter['id']);
        
        return $this;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildGlobalAwards(&$institutionMedicalCenter)
    {
        $institutionMedicalCenter['globalAwards'] = $this->getGlobalAwardsByInstitutionMedicalCenterId($institutionMedicalCenter['id']);
        
        return $this;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildOfferedServices(&$institutionMedicalCenter)
    {
        $institutionMedicalCenter['offeredServices'] = $this->getOfferedServicesByInstitutionMedicalCenterId($institutionMedicalCenter['id']);
        
        return $this;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildInstitutionSpecializations(&$institutionMedicalCenter)
    {
        $institutionMedicalCenter['institutionSpecializations'] = $this->getInstitutionSpecializationsByInstitutionMedicalCenterId($institutionMedicalCenter['id']);
        
        return $this;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildBusinessHours(&$institutionMedicalCenter)
    {
        $institutionMedicalCenter['businessHours'] = $this->getBusinessHoursByInstitutionMedicalCenterId($institutionMedicalCenter['id']);
        
        return $this;
    }
    
    public function getBusinessHoursByInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:BusinessHour')
            ->getByInstitutionMedicalCenter($institutionMedicalCenterId, Query::HYDRATE_ARRAY);
    }
    
    public function getOfferedServicesByInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')
            ->getAllServicesByInstitutionMedicalCenter($institutionMedicalCenterId, Query::HYDRATE_ARRAY);
    }
    
    public function getGlobalAwardsByInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')
            ->getAllGlobalAwardsByInstitutionMedicalCenter($institutionMedicalCenterId, Query::HYDRATE_ARRAY);
    }
    
    public function getDoctorsByInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        return $this->doctrine->getRepository('DoctorBundle:Doctor')
            ->findByInstitutionMedicalCenter($institutionMedicalCenterId, Query::HYDRATE_ARRAY);
    }
    
    public function getInstitutionSpecializationsByInstitutionMedicalCenterId($institutionMedicalCenterId)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getActiveSpecializationsByInstitutionMedicalCenter($institutionMedicalCenterId, Query::HYDRATE_ARRAY);
    }
    
    /**
     * 
     * @param array $institutionMedicalCenter data in HYDRATE_ARRAY
     */
    public function listActiveSpecializations(array $institutionMedicalCenter)
    {
        $list = array();
        $institutionSpecializations = isset($institutionMedicalCenter['institutionSpecializations']) 
            ? $institutionMedicalCenter['institutionSpecializations']
            : array();
         
        foreach ($institutionSpecializations as $_each) {
            $specialization = $_each['specialization'];
            $list[$specialization['id']] = $specialization['name'];
        }
        
        return $list;
    }
    
    /**
     * Get query builder for a fully eagerloaded medical center
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderForFullInstitutionMedicalCenterProfile()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('imc, inst, co, st, ct, inst_lg, imc_lg, imc_m, imc_cd, imc_bh, inst_sp, sp, sp_lg, tr, sub_sp')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'imc')
            ->innerJoin('imc.institution', 'inst')
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.logo', 'inst_lg')
            ->leftJoin('imc.logo', 'imc_lg')
            ->leftJoin('imc.media', 'imc_m')
            ->leftJoin('imc.contactDetails', 'imc_cd')
            ->leftJoin('imc.businessHours', 'imc_bh')
            ->innerJoin('imc.institutionSpecializations', 'inst_sp')
            ->innerJoin('inst_sp.specialization', 'sp')
            ->leftJoin('sp.media', 'sp_lg')
            ->leftJoin('sp.treatments', 'tr')
            ->leftJoin('tr.subSpecializations', 'sub_sp')
            ->where('1=1')
            ->andWhere('imc.status = :imcActiveStatus')
                ->setParameter('imcActiveStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->andWhere('inst.status = :instActiveStatus')
                ->setParameter('instActiveStatus', InstitutionStatus::getBitValueForApprovedStatus());
                
        return $qb;
    }
}