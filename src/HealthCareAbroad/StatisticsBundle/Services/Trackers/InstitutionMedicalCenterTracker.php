<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class InstitutionMedicalCenterTracker extends Tracker
{
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        $type = $parameters->get(StatisticParameters::TYPE);
        $data = null;
        if (StatisticTypes::INSTITUTION_MEDICAL_CENTER != $type)
        {
            // not an advertisement statistics
            return null;
        }
        
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