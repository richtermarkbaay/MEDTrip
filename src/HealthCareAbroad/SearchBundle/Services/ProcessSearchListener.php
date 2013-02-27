<?php
namespace HealthCareAbroad\SearchBundle\Services;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use HealthCareAbroad\SearchBundle\Controller\FrontendController;

class ProcessSearchListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if ('HealthCareAbroad\SearchBundle\Controller\FrontendController::searchProcessAction' != $request->attributes->get('_controller')) {
            return;
        }

        $controllerAndAction = $event->getController();
        $controller = $controllerAndAction[0];

        $requestParams = $request->request->all();

        if (isset($requestParams['searchParameter']) && !empty($requestParams['searchParameter'])) {
            $event->setController(array($controller, 'searchProcessNarrowAction'));

            return;
        }

        $treatmentId = $request->get('treatment_id');
        $destinationId = $request->get('destination_id') == '0-0' ? 0 : $request->get('destination_id');
        $treatmentName = $request->get('sb_treatment');
        $destinationName = $request->get('sb_destination');

        //TODO: this will break if we are going to force the destination field in our search forms to always submit the ID
        if ((!$treatmentId && !$destinationId) || (!$treatmentId && $treatmentName) || (!$destinationId && $destinationName)) {

            $context = '';
            if ($treatmentName && $destinationName) {
                $context = 'combined';
            } elseif ($treatmentName) {
                $context = 'treatment';
            } elseif ($destinationName) {
                $context = 'destination';
            }

            $request->attributes->set('context', $context);
            $event->setController(array($controller, 'searchProcessKeywordsAction'));

            return;
        }
    }

    //Unused
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ('HealthCareAbroad\SearchBundle\Controller\FrontendController::searchProcessAction' != $request->attributes->get('_controller')) {
            return;
        }

        if (true) {
            //$request->attributes->set('_controller', $controller);
        }
    }
}
