<?php

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CommonPageController extends Controller
{
    public function viewPrivacyPolicyAction(){
        
        return $this->render('FrontendBundle:Static:privacyPolicy.html.twig');
    }
    
    public function viewTermsOfUseAction(){
    
        return $this->render('FrontendBundle:Static:termsOfUse.html.twig');
    }
}