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
}