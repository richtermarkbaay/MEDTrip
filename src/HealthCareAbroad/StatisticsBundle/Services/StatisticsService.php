<?php

namespace HealthCareAbroad\StatisticsBundle\Services;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionTracker;

use Symfony\Component\HttpFoundation\Request;

class StatisticsService 
{
    private static $statsCategoriesByRoutes = array(
        'frontend_institution_multipleCenter_profile' => StatisticCategories::HOSPITAL_FULL_PAGE_VIEW,
        'frontend_institution_singleCenter_profile' => StatisticCategories::HOSPITAL_FULL_PAGE_VIEW,
        'frontend_institutionMedicalCenter_profile' => StatisticCategories::CLINIC_FULL_PAGE_VIEW
    );

    public function addPageViewStats(Request $request)
    {
        $route = $request->attributes->get('_route');
        

    }

    private function addInstitutionFullPageViewStat($slug)
    {
        $tracker = InstitutionTracker::createInstance();
        $parameterBag = new StatisticsParameterBag($request->attributes->all());
        $data = $tracker->createDataFromParameters($parameterBag);
        var_dump($data);
        echo $route;
        var_dump($tracker);
        exit;
    }
    
    
}