<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;
final class AdvertisementTypes
{
    const NEWS_TICKER = 1;
    
    const FEATURED_INSTITUTION = 2;
    
    const FEATURED_LISTING = 3;
    
    
    static public function getList()
    {
        return array(
            self::NEWS_TICKER => 'News Ticker',
            self::FEATURED_INSTITUTION => 'Featured Institution',
            self::FEATURED_LISTING => 'Featured Listing'
        );
    }
    
    /**
     * Get the class mapping for Advertisement type
     * 
     * @static
     * @access public
     * @return array
     */
    static public function getDiscriminatorMapping()
    {
        return  array(
            AdvertisementTypes::NEWS_TICKER => 'HealthCareAbroad\AdvertisementBundle\Entity\NewsTickerAdvertisement',
            AdvertisementTypes::FEATURED_INSTITUTION => 'HealthCareAbroad\AdvertisementBundle\Entity\FeaturedInstitutionAdvertisement',
            AdvertisementTypes::FEATURED_LISTING => 'HealthCareAbroad\AdvertisementBundle\Entity\FeaturedListingAdvertisement'
        );
    }
}