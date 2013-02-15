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
    
    // FIXME: inappropriate, just quick implementation
    private $staticHomepageAdvertisementTypes = array(
        1 => 'Premier Home Page Feature',
        2 => 'Home Page Clinic Feature',
        5 => 'Home Page Featured Video',
        6 => 'Featured Post'
    );
    
    private $retrievedAdvertisementsByType = array();
    
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function getHomepageAdvertisementByType($type)
    {
        $this->_retrieveHomepageAds();
        
        return \array_key_exists($type, $this->retrievedAdvertisementsByType)
            ? $this->retrievedAdvertisementsByType[$type]
            : array();
    }
    
    public function getHomepageAdvertisements()
    {
        $this->_retrieveHomepageAds();
        
        //return $this->retrievedAdvertisementsByType;
    }
    
    private function _retrieveHomepageAds()
    {
        static $hasFetched = false;
        if ($hasFetched) {
            return;
        }
        
        $advertisements = $this->doctrine->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')
            ->getActiveAdvertisementsByType(\array_keys($this->staticHomepageAdvertisementTypes));
        
        foreach ($advertisements as $each){
            $typeId = $each->getAdvertisementType()->getId();
            if (!\array_key_exists($typeId, $this->retrievedAdvertisementsByType)) {
                $this->retrievedAdvertisementsByType[$typeId] = array();
            }
            $this->retrievedAdvertisementsByType[$typeId][] = $each;
        }

        $hasFetched = true;
    }
}