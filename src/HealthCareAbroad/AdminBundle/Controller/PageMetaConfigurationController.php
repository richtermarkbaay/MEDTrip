<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\HelperBundle\Form\PageMetaConfigurationFormType;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

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
        $request->getSession()->setFlash('redirect_url', $this->generateUrl($request->attributes->get('_route')));
        
        return $this->render('AdminBundle:PageMetaConfiguration:resultspage.index.html.twig');
    }
    
    public function institutionPageMetaConfigurationAction(Request $request)
    {
        $request->getSession()->setFlash('redirect_url', $this->generateUrl($request->attributes->get('_route')));
        
        return $this->render('AdminBundle:PageMetaConfiguration:institution_page.html.twig');
    }
    
    public function ajaxProcessSearchParametersAction(Request $request)
    {
        $searchParameterService = $this->get('services.search.parameters');
        $compiledSearch = $searchParameterService->compileRequest($request);
        // no app_dev.php
        $url = $compiledSearch->getUrl();
        
        $metaConfiguration = $this->get('services.helper.page_meta_configuration')
            ->findOneByUrl($url);
        
        if (!$metaConfiguration) {
            $metaConfiguration = new PageMetaConfiguration();
            $metaConfiguration->setUrl($url);
            $metaConfiguration->setPageType(PageMetaConfiguration::PAGE_TYPE_SEARCH_RESULTS);
        }
        
        $form = $this->createForm(new PageMetaConfigurationFormType(), $metaConfiguration);
        $html = $this->renderView('AdminBundle:PageMetaConfiguration:form.html.twig', array('form' => $form->createView()));
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
    public function saveAction(Request $request)
    {
        $id = $request->get('id', 0);
        if ($id) {
            $metaConfiguration = $this->getDoctrine()->getRepository('HelperBundle:PageMetaConfiguration')->find($id);
            if (!$metaConfiguration) {
                throw $this->createNotFoundException('Invalid Page meta configuration');
            }
        }
        else {
            $metaConfiguration = new PageMetaConfiguration();
        }
        
        $form = $this->createForm(new PageMetaConfigurationFormType(), $metaConfiguration);
        $form->bind($request);
        $redirectUrl = $request->getSession()->hasFlash('redirect_url')
            ? $request->getSession()->getFlash('redirect_url')
            : $this->generateUrl('admin_page_metas_index');
        
        if ($form->isValid()) {
            $this->get('services.helper.page_meta_configuration')
                ->save($form->getData());
            
            $request->getSession()->setFlash('success', 'Meta configuration for '.$form->getData()->getUrl().' page saved.');
        }
        else {
            $request->getSession()->setFlash('error', 'Failed to save page meta configuration');
        }
        
        return $this->redirect($redirectUrl);
    }
}