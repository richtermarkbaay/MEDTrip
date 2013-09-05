<?php

namespace HealthCareAbroad\ApiBundle\Services;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionGalleryService;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;

class InstitutionMedicalCenterApiService
{
    // possible contexts to consider when building the data
    const CONTEXT_FULL_API = 0; // not implemented, but maybe used for api with no restrictions
    
    const CONTEXT_FULL_PAGE_VIEW = 1; // Full page
    
    const CONTEXT_HOSPITAL_CLINICS_LIST = 2; // Clinic list
    
    const CONTEXT_SEARCH_RESULT_ITEM = 3; // Search results
    
    const CONTEXT_ADS = 4; // Ads results
    
    
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var MediaExtension
     */
    private $mediaExtensionService;
    
    /**
     * @var ContactDetailService
     */
    private $contactDetailService;
    
    /** 
     * @var InstitutionGalleryService
     */
    private $institutionGalleryService;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function setMediaExtension(MediaExtension $v)
    {
        $this->mediaExtensionService = $v;
    }
    
    public function setContactDetailService(ContactDetailService $v)
    {
        $this->contactDetailService = $v;
    }
    
    public function setInstitutionGalleryService(InstitutionGalleryService $service)
    {
        $this->institutionGalleryService = $service;
    }
    
    /**
     * 
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildContactDetails(&$institutionMedicalCenter, $context=InstitutionMedicalCenterApiService::CONTEXT_FULL_PAGE_VIEW)
    {
        $institutionMedicalCenter['mainContactNumber'] = null;

        $canDisplayContactDetails = PayingStatus::FREE_LISTING != $institutionMedicalCenter['payingClient'];
        if ($canDisplayContactDetails) {
            // add a string representation for each contactDetail
            $hasSetMainContact = false;
            foreach ($institutionMedicalCenter['contactDetails'] as &$contactDetail) {
                $contactDetail['__toString'] = $this->contactDetailService->contactDetailToString($contactDetail);
                if (!$hasSetMainContact) {
                    $institutionMedicalCenter['mainContactNumber'] = $contactDetail;
                    $hasSetMainContact = true;
                }
            }   
        }
        else {
            // not yet used, but could be helpful
            if (self::CONTEXT_FULL_API != $context) {
                // TODO: Note to self: do we really have to clear this?  
                $institutionMedicalCenter['contactDetails'] = array();
            }
        }
        
        return $this;
    }
    
    /**
     * Build website url and social media sites url
     * 
     * @param array $institutionMedicalCenter
     * @param int $context
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildExternalSites(&$institutionMedicalCenter, $context=InstitutionMedicalCenterApiService::CONTEXT_FULL_PAGE_VIEW)
    {
        $institutionMedicalCenter['socialMediaSites'] = \json_decode($institutionMedicalCenter['socialMediaSites'], true);

        // api with restrictions 
        if (self::CONTEXT_FULL_API != $context) {
 
            $canDisplayExternalSites = PayingStatus::FREE_LISTING != $institutionMedicalCenter['payingClient'];

            if(!$canDisplayExternalSites) {
                // build the Social Media Sites default values
                $institutionMedicalCenter['socialMediaSites'] = SocialMediaSites::getDefaultValues();
                $institutionMedicalCenter['websites'] = null;
            }
        }
        
        

        return $this;
    }
    
    /**
     *
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildFeaturedMediaSource(&$institutionMedicalCenter)
    {
        $canDisplayFeaturedMedia = PayingStatus::FREE_LISTING != $institutionMedicalCenter['payingClient'];
        
        // temporarily pull the institution featured media
        $featuredMedia = isset($institutionMedicalCenter['institution']['featuredMedia']) ? $institutionMedicalCenter['institution']['featuredMedia'] : null;
        
        if($canDisplayFeaturedMedia && $featuredMedia){
            $featuredMedia['src'] = $this->mediaExtensionService->getInstitutionMediaSrc($featuredMedia, ImageSizes::LARGE_BANNER);;
            $institutionMedicalCenter['featuredMedia'] = $featuredMedia;
        } 
        
        return $this;
    }
    
    /**
     * Build media gallery data of medical center
     * 
     * @param array $institutionMedicalCenter
     * @param int $context
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildMediaGallery(&$institutionMedicalCenter, $context=InstitutionMedicalCenterApiService::CONTEXT_FULL_PAGE_VIEW)
    {
        $canDisplayGallery = PayingStatus::FREE_LISTING != $institutionMedicalCenter['payingClient'];

        if (!$canDisplayGallery && $context != InstitutionMedicalCenterApiService::CONTEXT_FULL_API) {
            $institutionMedicalCenter['media']['photos'] = array();
        } else {
            $institutionMedicalCenter['media']['photos'] = $this->institutionGalleryService->getInstitutionMedicalCenterPhotos($institutionMedicalCenter['id']);            
        }
        
        return $this;
    }
    
    /**
     * 
     * @param array $institutionMedicalCenter
     * @return \HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService
     */
    public function buildLogoSource(&$institutionMedicalCenter, $size=ImageSizes::MINI, $context=InstitutionMedicalCenterApiService::CONTEXT_FULL_PAGE_VIEW)
    {
        $canDisplayImcLogo = PayingStatus::FREE_LISTING != $institutionMedicalCenter['payingClient']; // default
        $canDefaultToSpecializationLogo = false;
        switch ($context){
            case InstitutionMedicalCenterApiService::CONTEXT_ADS:
                break;
            case InstitutionMedicalCenterApiService::CONTEXT_SEARCH_RESULT_ITEM:
                break;
            case InstitutionMedicalCenterApiService::CONTEXT_HOSPITAL_CLINICS_LIST:
                $canDefaultToSpecializationLogo = true;
                break;
            case InstitutionMedicalCenterApiService::CONTEXT_FULL_PAGE_VIEW:
                $canDefaultToSpecializationLogo = true;
                break;
        }
        
        
        // client is allowed to display logo, and there is a logo
        if ($canDisplayImcLogo && $institutionMedicalCenter['logo']) {
            $institutionMedicalCenter['logo']['src'] = $this->mediaExtensionService->getInstitutionMediaSrc($institutionMedicalCenter['logo'], $size);
        }
        else {
            $src = null;
            if ($canDefaultToSpecializationLogo) {
                // not allowed to display logo, or clinic has no logo
                // we get the logo of the first specialization
                $firstSpecialization = \count($institutionMedicalCenter['institutionSpecializations'])
                ? (isset($institutionMedicalCenter['institutionSpecializations'][0]['specialization']) ? $institutionMedicalCenter['institutionSpecializations'][0]['specialization'] : null)
                : null;
                
                // first specialization has a media
                if ($firstSpecialization && $firstSpecialization['media']){
                    $src = $this->mediaExtensionService->getSpecializationMediaSrc($firstSpecialization['media'], ImageSizes::SPECIALIZATION_DEFAULT_LOGO);
                }
            }
            $institutionMedicalCenter['logo']['src'] = $src;
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
        
        // build logo of insitution specializations
        foreach ($institutionMedicalCenter['institutionSpecializations'] as &$instSpecialization) {
            if ($instSpecialization['specialization']['media']) {
                $instSpecialization['specialization']['media']['src'] = $this->mediaExtensionService->getSpecializationMediaSrc($instSpecialization['specialization']['media'], ImageSizes::SPECIALIZATION_DEFAULT_LOGO);
            }
        }
        
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
        $qb->select('imc, inst, co, st, ct, inst_lg, imc_lg, inst_fm, imc_m, imc_cd, imc_bh, inst_sp, sp, sp_lg, tr, sub_sp')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'imc')
            ->innerJoin('imc.institution', 'inst')
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.logo', 'inst_lg')
            ->leftJoin('inst.featuredMedia', 'inst_fm')
            ->leftJoin('imc.logo', 'imc_lg')
            ->leftJoin('imc.media', 'imc_m')
            ->leftJoin('imc.contactDetails', 'imc_cd')
            ->leftJoin('imc.businessHours', 'imc_bh')
            ->leftJoin('imc.institutionSpecializations', 'inst_sp')
            ->leftJoin('inst_sp.specialization', 'sp')
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