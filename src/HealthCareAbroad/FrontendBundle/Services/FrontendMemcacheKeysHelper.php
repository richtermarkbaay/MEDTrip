<?php 

namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

class FrontendMemcacheKeysHelper
{
    // ADS Memcache Keys
    const HOMEPAGE_PREMIER_ADS_KEY = 'frontend.homepage_premier_ads';
    const HOMEPAGE_FEATURED_CLINICS_ADS_KEY = 'frontend.homepage_featured_clinics_ads';
    const HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY = 'frontend.homepage_featured_destinations_ads';
    const HOMEPAGE_FEATURED_POSTS_ADS_KEY = 'frontend.homepage_featured_posts_ads';
    const HOMEPAGE_FEATURED_VIDEO_ADS_KEY = 'frontend.homepage_featured_video_ads';
    const HOMEPAGE_COMMON_TREATMENTS_ADS_KEY = 'frontend.homepage_common_treatments_ads';

    // Institution/InstitutionMedicalCenter Memcache Keys
    const INSTITUTION_PROFILE_KEY = 'frontend.controller.institution_profile.{ID}';
    const INSTITUTION_MEDICAL_CENTER_PROFILE_KEY = 'frontend.controller.institutionMedicalCenter:profile.{ID}';

    static function generateInsitutionProfileKey($instititionId = '')
    {
        return str_replace('{ID}', $instititionId, self::INSTITUTION_PROFILE_KEY);
    }

    static function generateInsitutionMedicalCenterProfileKey($centerId = '')
    {
        return str_replace('{ID}', $centerId, self::INSTITUTION_MEDICAL_CENTER_PROFILE_KEY);
    }

    /** 
     * @param AdvertisementType $advertisementType
     * @return Ambigous <NULL, string>
     */
    static function getAdvertisementKeyByType(AdvertisementType $advertisementType)
    {
        $types = array(
            AdvertisementTypes::HOMEPAGE_PREMIER => self::HOMEPAGE_PREMIER_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_CLINIC => self::HOMEPAGE_FEATURED_CLINICS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_DESTINATION => self::HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_POST => self::HOMEPAGE_FEATURED_POSTS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_VIDEO => self::HOMEPAGE_FEATURED_VIDEO_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_COMMON_TREATMENT => self::HOMEPAGE_COMMON_TREATMENTS_ADS_KEY
        );

        return isset($types[$advertisementType->getId()]) ? $types[$advertisementType->getId()] : null; 
    }
}