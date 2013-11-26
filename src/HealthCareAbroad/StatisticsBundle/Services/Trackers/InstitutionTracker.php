<?php

namespace HealthCareAbroad\StatisticsBundle\Services\Trackers;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticsDaily;
use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;
use HealthCareAbroad\StatisticsBundle\Entity\InstitutionStatisticsDaily;
use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

class InstitutionTracker extends Tracker
{
    static function getCategoryByRoute($route)
    {
        $categories = array(
            'frontend_institution_multipleCenter_profile' => StatisticCategories::HOSPITAL_FULL_PAGE_VIEW,
            'frontend_institution_singleCenter_profile' => StatisticCategories::HOSPITAL_FULL_PAGE_VIEW
        );

        return isset($categories[$route]) ? $categories[$route] : null; 
    }
    
    public function createDataFromParameters(StatisticsParameterBag $parameters)
    {
        $institutionStatsDaily = new InstitutionStatisticsDaily();
        $institutionStatsDaily->setInstitutionId($parameters->get(StatisticParameters::INSTITUTION_ID));
        $institutionStatsDaily->setCategoryId($parameters->get(StatisticParameters::CATEGORY_ID));
        $institutionStatsDaily->setIpAddress($parameters->get(StatisticParameters::IP_ADDRESS));
        $institutionStatsDaily->setDate(new \DateTime());

        return $institutionStatsDaily;
    }

    /**
     * (non-PHPdoc)
     * @see \HealthCareAbroad\StatisticsBundle\Services\Trackers\Tracker::createDataFromHttpRequest()
     */
    public function createDataFromHttpRequest(Request $request)
    {
        $requestParams = $request->attributes->all();
        $category = self::getCategoryByRoute($request->get('_route'));

        switch($category) {
            case StatisticCategories::HOSPITAL_FULL_PAGE_VIEW :
                $params[StatisticParameters::INSTITUTION_ID] = $requestParams['institution']['id'];
                break;
        }

        $params[StatisticParameters::IP_ADDRESS] = $request->getClientIp();
        $params[StatisticParameters::CATEGORY_ID] = $category;

        return $this->createDataFromParameters(new StatisticsParameterBag($params));
    }

    public function add(StatisticsDaily $data)
    {
        // extra check that this is an institution statistics data
        if (!$data instanceof InstitutionStatisticsDaily) {
            return false;
        }

        $this->data[] = $data;

        return true;
    }
}