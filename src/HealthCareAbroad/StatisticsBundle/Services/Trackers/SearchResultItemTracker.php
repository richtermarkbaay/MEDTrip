<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\SearchResultsItemStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class SearchResultItemTracker extends Tracker
{
    public function createDataFromHttpRequest(Request $request)
    {
        
    }
    
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        $type = $parameters->get(StatisticParameters::TYPE);
        $data = null;
        if (StatisticTypes::SEARCH_RESULT_ITEM != $type)
        {
            return null;
        }
        
        $data = new SearchResultsItemStatisticsDaily();
        $data->setCategoryId($parameters->get(StatisticParameters::CATEGORY_ID, 0));
        $data->setDate(new \DateTime(\date('Y-m-d')));
        $data->setInstitutionId($parameters->get(StatisticParameters::INSTITUTION_ID));
        $data->setInstitutionMedicalCenterId($parameters->get(StatisticParameters::INSTITUTION_MEDICAL_CENTER_ID, 0));
        $data->setCityId($parameters->get(StatisticParameters::CITY_ID, 0));
        $data->setCountryId($parameters->get(StatisticParameters::COUNTRY_ID, 0));
        $data->setSpecializationId($parameters->get(StatisticParameters::SPECIALIZATION_ID, 0));
        $data->setSubSpecializationId($parameters->get(StatisticParameters::SUB_SPECIALIZATION_ID, 0));
        $data->setTreatmentId($parameters->get(StatisticParameters::TREATMENT_ID, 0));
        
        return $data;
    }
    
    public function add(StatisticsDaily $data)
    {
        if (!$data instanceof SearchResultsItemStatisticsDaily) {
            return false;
        }

        $this->data[] = $data;

        return true;
    }
}