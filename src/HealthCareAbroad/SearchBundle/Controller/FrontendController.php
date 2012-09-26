<?php
namespace HealthCareAbroad\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function showWidgetAction(Request $request)
    {
        $context = $request->get('context');

        switch ($context) {
            case 'homepage':
                //$form = $this->createForm();
                $template = 'SearchBundle:Frontend:searchWidget.html.twig';
                break;

            default:
                throw new \Exception('Undefined context');
        }

        return $this->render($template);
    }

    /**
     * There are three scenarios that will land us in this action:
     *
     *
     * @param Request $request
     */
    public function searchHomepageAction(Request $request)
    {

        var_dump($request->getQueryString()); exit;
    }

    public function ajaxLoadTreatmentsAction(Request $request)
    {
        $result = $this->get('services.search')->getJsonEncodedTreatmentsByName($request->get('term', ''), $request->get('prevTerm'));

        return new Response($result, 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxLoadDestinationsAction(Request $request)
    {
        $result = $this->get('services.search')->getJsonEncodedDestinationsByName($request->get('term', ''), $request->get('prevTerm'));

        return new Response($result, 200, array('Content-Type'=>'application/json'));
    }

}