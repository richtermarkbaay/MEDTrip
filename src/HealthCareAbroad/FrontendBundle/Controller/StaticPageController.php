<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\AdminBundle\Entity\StaticPage;

use HealthCareAbroad\HelperBundle\Form\StaticPageFormType;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class StaticPageController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('FrontendBundle:StaticPage:index.html.twig', array(
                        'title' => 'asd',
                        'content' => 'asasd' ));
    }
    
}
