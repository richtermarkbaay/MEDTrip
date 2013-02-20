<?php

namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    public function ajaxLoadSearchSourcesAction(Request $request)
    {
        $responseData = array();
        switch ($request->get('type', 'all')) {
            case 'treatments':
                break;
            case 'destinations':
                break;
            default:
                // defaults to loading all
                $responseData = array(
                    'treatments' => $this->get('services.search')->getAllTreatments(),
                    'destinations' => $this->get('services.search')->getAllDestinations(),
                );
        }
        
        return new Response(\json_encode($responseData), 200, array('content-type' => 'application/json'));
    }
}