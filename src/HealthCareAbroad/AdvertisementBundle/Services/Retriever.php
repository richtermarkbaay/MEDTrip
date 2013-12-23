<?php
namespace HealthCareAbroad\AdvertisementBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Retriever for active ads
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class Retriever
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    private $adevertisementDenormalizedRepo;
    
    private $retrievedAdvertisementsByType = array();

    static function getHomepageAdvertisementTypes()
    {
        return array(
            AdvertisementTypes::HOMEPAGE_PREMIER => 'Premier Home Page Feature',
            AdvertisementTypes::HOMEPAGE_FEATURED_DESTINATION => 'Home Page Featured Destination',
            AdvertisementTypes::HOMEPAGE_FEATURED_VIDEO => 'Home Page Featured Video',
            AdvertisementTypes::HOMEPAGE_FEATURED_POST => 'Home Page Featured Post'
        );
    }

    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;

        $this->adevertisementDenormalizedRepo = $v->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty');
    }

    // Homepage Premier Homepage Ads
    public function getHomepagePremierAdvertisements()
    {
        $ads = $this->getHomepageAdvertisementByType(AdvertisementTypes::HOMEPAGE_PREMIER);
        $key = array_rand($ads);

        return count($ads) ? $ads[$key] : null;
    }

    // Homepage Featured Clinics
    public function getHomepageFeaturedClinics()
    {
        $criteria = array('advertisementType' => AdvertisementTypes::HOMEPAGE_FEATURED_CLINIC);

        return $this->adevertisementDenormalizedRepo->getActiveFeaturedClinicByCriteria($criteria);
    }

    // Homepage Featured Destinations Ads
    public function getHomepageFeaturedDestinations()
    {
        return $this->getHomepageAdvertisementByType(AdvertisementTypes::HOMEPAGE_FEATURED_DESTINATION);
    }
    
    // Homepage Featured Posts Ads
    public function getHomepageFeaturedPosts()
    {
        return $this->getHomepageAdvertisementByType(AdvertisementTypes::HOMEPAGE_FEATURED_POST);
    }

    // Homepage Common Treatments Ads
    public function getHomepageCommonTreatments()
    {
        return $this->adevertisementDenormalizedRepo->getCommonTreatments();
    }

    // Homepage Featured Video Ads
    public function getHomepageFeaturedVideo()
    {
        $video = null;
        $videos = $this->getHomepageAdvertisementByType(AdvertisementTypes::HOMEPAGE_FEATURED_VIDEO);
        $key = array_rand($videos);
        
        if(count($videos) && $youtubeId = $this->_getYoutubeId($videos[$key])) {
            $video = array('advertisement' => $videos[$key], 'youtubeId' => $youtubeId);
        }

        return $video;
    }

    // Search Results Image Ads
    public function getSearchResultsImageAds(array $criteria = array(), $limit = 1)
    {
        return $this->adevertisementDenormalizedRepo->getActiveSearchResultsImageAds($criteria, $limit);
    }
    
    
    // Search Results Featured Clinic by Criteria
    public function getSearchResultsFeaturedClinicByCriteria(array $criteria = array(), $limit = 1)
    {
        if(!count($criteria)) {
            return null;
        }

        if(isset($criteria['specializationId'])) {

            $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_SPECIALIZATION_FEATURE;
            
            if(isset($criteria['countryId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_COUNTRY_SPECIALIZATION_FEATURE; 
            } else if(isset($criteria['cityId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURE; 
            }

        } else if(isset($criteria['subSpecializationId'])) {

            $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_SUBSPECIALIZATION_FEATURE;
            
            if(isset($criteria['countryId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_FEATURE;
            } else if(isset($criteria['cityId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURE;
            }

        } else if(isset($criteria['treatmentId'])) {

            $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_TREATMENT_FEATURE;
            
            if(isset($criteria['countryId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_COUNTRY_TREATMENT_FEATURE; 
            } else if(isset($criteria['cityId'])) {
                $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_CITY_TREATMENT_FEATURE;
            }
        }

        return $this->adevertisementDenormalizedRepo->getActiveFeaturedClinicByCriteria($criteria, $limit);
    }
    
    // Search Results Featured Intitution by Criteria
    public function getSearchResultsFeaturedInstitutionByCriteria(array $criteria = array(), $limit = 1)
    {
        if(empty($criteria)) {
            return null;
        }

        if(isset($criteria['countryId'])) {
            $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_COUNTRY_FEATURE;
    
        } else if(isset($criteria['cityId'])) {
            $criteria['advertisementType'] = AdvertisementTypes::SEARCH_RESULTS_CITY_FEATURE;
        }

        return $this->adevertisementDenormalizedRepo->getActiveFeaturedInstitutionByCriteria($criteria, $limit);
    }

    public function getHomepageAdvertisementByType($type)
    {
        $this->_retrieveHomepageAds();
        
        return \array_key_exists($type, $this->retrievedAdvertisementsByType)
            ? $this->retrievedAdvertisementsByType[$type]
            : array();
    }
    
    
    private function _retrieveHomepageAds()
    {
        static $hasFetched = false;
        if ($hasFetched) {
            return;
        }

        $adTypeIds = \array_keys(self::getHomepageAdvertisementTypes());
        $advertisements = $this->adevertisementDenormalizedRepo->getActiveAdvertisementsByType($adTypeIds);

        foreach ($advertisements as $each){
            $typeId = $each->getAdvertisementType()->getId();
            if(!isset($this->retrievedAdvertisementsByType[$typeId])) {
                $this->retrievedAdvertisementsByType[$typeId] = array();
            }
            $this->retrievedAdvertisementsByType[$typeId][] = $each;
        }

        $hasFetched = true;
    }
    
    private function _getYoutubeId($video)
    {
        $youtubeId = null;
        
        if($video->getVideoUrl()) {
            \preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$video->getVideoUrl(),$videoMatches);
        
            $youtubeId = $videoMatches[1];
        }

        return $youtubeId;
    }
}