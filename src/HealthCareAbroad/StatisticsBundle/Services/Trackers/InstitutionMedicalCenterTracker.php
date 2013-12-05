<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\StatisticsBundle\Entity\InstitutionMedicalCenterStatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class InstitutionMedicalCenterTracker extends Tracker
{
    static function getCategoryByRoute($route)
    {
        $categories = array(
            'frontend_institutionMedicalCenter_profile' => StatisticCategories::CLINIC_FULL_PAGE_VIEW
        );
    
        return isset($categories[$route]) ? $categories[$route] : null;
    }

    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        $centerStatsDaily = new InstitutionMedicalCenterStatisticsDaily();
        $centerStatsDaily->setInstitutionId($parameters->get(StatisticParameters::INSTITUTION_ID));
        $centerStatsDaily->setInstitutionMedicalCenterId($parameters->get(StatisticParameters::INSTITUTION_MEDICAL_CENTER_ID));
        $centerStatsDaily->setCategoryId($parameters->get(StatisticParameters::CATEGORY_ID));
        $centerStatsDaily->setIpAddress($parameters->get(StatisticParameters::IP_ADDRESS));
        $centerStatsDaily->setDate(new \DateTime());

        return $centerStatsDaily;
    }

    /**
     * (non-PHPdoc)
     * @see \HealthCareAbroad\StatisticsBundle\Services\Trackers\Tracker::createDataFromHttpRequest()
     */
    public function createDataFromHttpRequest(Request $request)
    {
        $requestParams = $request->attributes->all();
        $category = self::getCategoryByRoute($request->get('_route'));
        
        if(!$category)
            return null;

        switch($category) {
            case StatisticCategories::CLINIC_FULL_PAGE_VIEW :
                $params[StatisticParameters::INSTITUTION_ID] = $requestParams['institutionMedicalCenter']['institution']['id'];
                $params[StatisticParameters::INSTITUTION_MEDICAL_CENTER_ID] = $requestParams['institutionMedicalCenter']['id'];
                break;
        }

        $params[StatisticParameters::IP_ADDRESS] = $request->getClientIp();
        $params[StatisticParameters::CATEGORY_ID] = $category;

         return $this->createDataFromParameters(new StatisticsParameterBag($params));
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