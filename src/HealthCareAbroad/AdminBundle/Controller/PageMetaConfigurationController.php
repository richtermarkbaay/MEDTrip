<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageMetaConfigurationController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->resultsPageMetaConfigurationAction($request);
    }
    
    public function resultsPageMetaConfigurationAction(Request $request)
    {
        return $this->render('AdminBundle:PageMetaConfiguration:resultspage.index.html.twig');
    }
    
    public function ajaxProcessSearchParametersAction(Request $request)
    {
        $searchParameterService = $this->get('services.search.parameters');
        $compiledSearch = $searchParameterService->compileRequest($request);
        echo $compiledSearch->getUrl();
        exit;
        
    }
}