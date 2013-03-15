<?php

namespace HealthCareAbroad\AdminBundle\Controller;

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
}