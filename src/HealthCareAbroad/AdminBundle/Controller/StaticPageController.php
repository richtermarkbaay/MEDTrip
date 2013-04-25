<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Entity\StaticPage;

use HealthCareAbroad\HelperBundle\Form\StaticPageFormType;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class StaticPageController extends Controller
{
    public function createStaticPageAction(Request $request)
    {
        $staticPage = new StaticPage();
        $form = $this->createForm(new StaticPageFormType(), $staticPage);
        
        if($request->isMethod('POST')) {
            $form->bind($request);
            if($form->isValid()) {
                $url = $this->get('services.static_page')->getUrlforStaticPagebyTitleAndSection($staticPage->getTitle(), $staticPage->getWebsiteSection());
                $staticPage->setUrl($url);
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($staticPage);
                $em->flush();
                
            }
        }
        return $this->render('AdminBundle:StaticPage:form.html.twig', array(
                        'form' => $form->createView()));
    }
    
    public function indexAction(Request $request)
    {
        return $this->render('FrontendBundle:StaticPage:index.html.twig', array(
                        'title' => 'asd',
                        'content' => 'asasd' ));
    }
    
}
