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
    
    // FIXME: inappropriate, just quick implementation
    private $staticHomepageAdvertisementTypes = array(
        1 => 'Premier Home Page Feature',
        3 => 'Home Page Featured Destination',
        5 => 'Home Page Featured Video',
        6 => 'Featured Post',
    );
    
    private $retrievedAdvertisementsByType = array();
    
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;

        $this->adevertisementDenormalizedRepo = $v->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty');
    }

    // Homepage Premier Homepage Ads
    public function getHomepagePremierAdvertisements()
    {
        $ads = $this->getHomepageAdvertisementByType(1);
        $key = array_rand($ads);

        return count($ads) ? $ads[$key] : null;
    }

    // Homepage Featured Clinics
    public function getHomepageFeaturedClinics()
    {
        return $this->adevertisementDenormalizedRepo->getActiveFeaturedClinic();
    }

    // Homepage Featured Destinations Ads
    public function getHomepageFeaturedDestinations()
    {
        return $this->getHomepageAdvertisementByType(3);
    }
    
    // Homepage Featured Posts Ads
    public function getHomepageFeaturedPosts()
    {
        return $this->getHomepageAdvertisementByType(6);
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
        $videos = $this->getHomepageAdvertisementByType(5);
        $key = array_rand($videos);
        
        if(count($videos) && $youtubeId = $this->_getYoutubeId($videos[$key])) {
            $video = array('advertisement' => $videos[$key], 'youtubeId' => $youtubeId);
        }

        return $video;
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
        
        $adTypeIds = \array_keys($this->staticHomepageAdvertisementTypes);
        $advertisements = $this->adevertisementDenormalizedRepo->getActiveAdvertisementsByType($adTypeIds);

        foreach ($advertisements as $each){
            $typeId = $each->getAdvertisementType()->getId();
            if (!\array_key_exists($typeId, $this->retrievedAdvertisementsByType)) {
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