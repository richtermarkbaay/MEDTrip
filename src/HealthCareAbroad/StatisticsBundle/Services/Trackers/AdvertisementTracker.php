<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\AdvertisementStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class AdvertisementTracker extends Tracker
{
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        $type = $parameters->get(StatisticParameters::TYPE);
        $data = null;
        if (StatisticTypes::ADVERTISEMENT != $type)
        {
            // not an advertisement statistics
            return null;
        }
        
        $data = new AdvertisementStatisticsDaily();
        $data->setAdvertisementId($parameters->get(StatisticParameters::ADVERTISEMENT_ID, 0));
        $data->setCategoryId($parameters->get(StatisticParameters::CATEGORY_ID, 0));
        $data->setDate(new \DateTime(\date('Y-m-d')));
        $data->setInstitutionId($parameters->get(StatisticParameters::INSTITUTION_ID));
        
        return $data;
    }
    
    public function createDataFromHttpRequest(Request $request)
    {
        
    }

    public function add(StatisticsDaily $data)
    {
        // extra check that this is an Advertisement statistics data
        if (!$data instanceof AdvertisementStatisticsDaily) {
            return false;
        }
        
        $this->data[] = $data;
        
        return true;
    }
}