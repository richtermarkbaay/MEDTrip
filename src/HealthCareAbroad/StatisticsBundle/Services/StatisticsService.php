<?php

namespace HealthCareAbroad\StatisticsBundle\Services;

use HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionTracker;

use Symfony\Component\HttpFoundation\Request;

class StatisticsService 
{
    public function addPageViewStats(Request $request)
    {
        $route = $request->attributes->get('_route');
        
        switch($route)
        {
            case 'frontend_multiple_center_institution_profile' :
            case 'frontend_single_center_institution_profile' :
                $slug = $request->get('institutionSlug');
                
                
                
                //$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->getInstitutionIdBySlug($slug);
                
                $tracker = InstitutionTracker::createInstance();
                $parameterBag = new StatisticsParameterBag($request->attributes->all());
                $data = $tracker->createDataFromParameters($parameterBag);
                var_dump($data);
                echo $route;
                var_dump($tracker);
                exit;
                break;
                
            default :
                return;
                break;
        }
    }
}