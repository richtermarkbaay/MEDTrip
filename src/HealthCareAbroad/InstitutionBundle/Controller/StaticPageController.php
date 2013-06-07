<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

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
        return $this->render('InstitutionBundle:StaticPage:index.html.twig', array(
                        'title' => 'asd',
                        'content' => 'asasd' ));
    }
    
    public function termsOfUseAction(Request $request)
    {
        return $this->render('InstitutionBundle:Main:terms_of_use.html.twig');
    }
    
    public function privacyPolicyAction(Request $request)
    {
        return $this->render('InstitutionBundle:Main:privacy_policy.html.twig');
    }
    
    public function faqAction(Request $request)
    {
        return $this->render('InstitutionBundle:Main:faq.html.twig');
    }
    
}
