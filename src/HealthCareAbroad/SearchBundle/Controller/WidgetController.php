<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    public function ajaxLoadSearchSourcesAction(Request $request)
    {
        $start = \microtime(true);

        $responseData = array();
        $defaultParameters = array(
            'destination' => null,
            'treatment' => null,
            'destinationLabel' => '',
            'treatmentLabel' => '',
            'filter' => $request->get('filter', '')
        );
        switch ($type = $request->get('type', 'all')) {
            case 'treatments':
                $defaultParameters['destination'] = $request->get('value', 0);
                $defaultParameters['destinationLabel'] = $request->get('label', '');
                $responseData[$type] = $this->get('services.search')->getTreatments(new SearchParameterBag($defaultParameters));
                break;
            case 'destinations':
                $defaultParameters['treatment'] = $request->get('value', 0);
                $defaultParameters['treatmentLabel'] = $request->get('label', '');
                $responseData[$type] = $this->get('services.search')->getDestinations(new SearchParameterBag($defaultParameters));
                break;
            default:
                $startDestinations = \microtime(true);
                $destinations = $this->get('services.search')->getAllDestinations();
                $endDestinations = \microtime(true);
                $responseData['destination_processing_time'] = $endDestinations-$startDestinations;

                $startTreatments = \microtime(true);
                $treatments = $this->get('services.search')->getAllTreatments();
                $endTreatments = \microtime(true);
                $responseData['treatment_processing_time'] = $endTreatments-$startTreatments;

                $responseData = array('treatments' => $treatments, 'destinations' => $destinations);
        }

        $end = \microtime(true);
        $responseData['executionTime'] = $end-$start;

        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
}