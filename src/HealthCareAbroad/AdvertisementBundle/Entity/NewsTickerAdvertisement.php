<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class NewsTickerAdvertisement extends Advertisement
{
	
	protected $typeLabel = 'News Ticker';
	
    public function __construct()
    {
        $this->type = AdvertisementTypes::NEWS_TICKER;
    }
}