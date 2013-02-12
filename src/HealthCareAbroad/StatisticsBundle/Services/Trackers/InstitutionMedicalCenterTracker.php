<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class InstitutionMedicalCenterTracker extends Tracker
{
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        
    }
    
    public function add(StatisticsDaily $data)
    {
        // extra check that this is an institution medical center statistics data
        if (!$data instanceof InstitutionMedicalCenterStatisticsDaily) {
            return false;
        }
    
        $this->data[] = $data;
    
        return true;
    }
}