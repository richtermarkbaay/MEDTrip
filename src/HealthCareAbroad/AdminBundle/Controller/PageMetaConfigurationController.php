<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

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
        $request->getSession()->setFlash('redirect_url', $this->generateUrl($request->attributes->get('_route')));
        
        $metaConfiguration = $this->get('services.helper.page_meta_configuration')->findOneByUrl('/');
        if (!$metaConfiguration) {
            $metaConfiguration= new PageMetaConfiguration();
            $metaConfiguration->setUrl('/');
        }
        $form = $this->createForm(new PageMetaConfigurationFormType(), $metaConfiguration);
        
        return $this->render('AdminBundle:PageMetaConfiguration:homepage.html.twig', array('form' => $form->createView()));
    }
    
    public function pageMetaConfigurationAction(Request $request)
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
        // load approved institutions by default
        $institutions = $this->get('services.institution.factory')->findAllApproved();
        $data = array();
        foreach ($institutions as $_each) {
            $data[] = array(
                'id' => $_each->getId(),
                'label' => $_each->getName()
            );
        }
        
        return $this->render('AdminBundle:PageMetaConfiguration:institution_page.html.twig', array(
            'institutionsJsonData' => \json_encode($data, JSON_HEX_APOS)
        ));
    }
    
    public function ajaxProcessInstitutionParametersAction(Request $request)
    {
        $institution = $this->get('services.institution.factory')->findById($request->get('institutionId', 0));
        if (!$institution) {
            throw $this->createNotFoundException('Cannot build metas for invalid institution');
        }
        $institutionMedicalCenter = null;
        if ($imcId = $request->get('imcId', 0)) {
            $institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($imcId);
            if (!$institutionMedicalCenter || $institutionMedicalCenter->getInstitution()->getId() != $institution->getId()) {
                throw $this->createNotFoundException('Cannot build metas for invalid institution medical center');
            }
        } 
        
        $pageMetaService = $this->get('services.helper.page_meta_configuration');
        $builderMethod = 'buildForInstitutionPage';
        $builderParameter = null;
        if ($institutionMedicalCenter) { // clinic page
            $url = $this->generateUrl('frontend_institutionMedicalCenter_profile', array(
                'institutionSlug' => $institution->getSlug(),
                'imcSlug' => $institutionMedicalCenter->getSlug()
            ));
            $builderMethod = 'buildForInstitutionMedicalCenterPage';
            $builderParameter = $institutionMedicalCenter;
        }
        else { // hospital page
            $url = $this->generateUrl(InstitutionService::getInstitutionRouteName($institution), array(
                'institutionSlug' => $institution->getSlug()
            ));
            $builderMethod = 'buildForInstitutionPage';
            $builderParameter = $institution;
        }
        
        // replace app_dev.php
        if ('appDevDebugProjectContainer' == get_class($this->get('service_container'))) {
            $url = \preg_replace('/^(\/app_dev\.php)/', '', $url);
        }
        
        // build meta config if no meta config available
        if (!$metaConfig = $pageMetaService->findOneByUrl($url)) {
            $metaConfig = $pageMetaService->{$builderMethod}($builderParameter);
            $metaConfig->setUrl($url);
            
            $pageMetaService->save($metaConfig);
        }
        
        $form = $this->createForm(new PageMetaConfigurationFormType(), $metaConfig);
        $html = $this->renderView('AdminBundle:PageMetaConfiguration:form.html.twig', array('form' => $form->createView()));
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
    public function ajaxProcessSearchParametersAction(Request $request)
    {
        $searchParameterService = $this->get('services.search.parameters');
        $compiledSearch = $searchParameterService->compileRequest($request);
        // no app_dev.php
        $url = $compiledSearch->getUrl();
        $metaConfigurationService = $this->get('services.helper.page_meta_configuration');
        $metaConfiguration = $metaConfigurationService->findOneByUrl($url);
        
        // no meta configuration saved, yet, create from builder
        if (!$metaConfiguration) {
            
            $searchVariables = array();
            // convert variable keys to the one accepted by the meta configuration service
            $searchUrlParameterKeyMapping = SearchUrlGenerator::getSearchParameterKeyToSearchUrlKeyMapping();
            foreach ($compiledSearch->getVariables() as $key => $searchVariable) {
                if (!\array_key_exists($key, $searchUrlParameterKeyMapping)) {
                    // this is a predefined set, so something must have gone wrong in the search parameter compiler, or the mapping has changed
                    throw new \Exception(\sprintf('Cannot map search parameter key "%s" to a search url key', $key));
                }
                $searchVariables[$searchUrlParameterKeyMapping[$key]] = $searchVariable;
            }
            $metaConfiguration = $metaConfigurationService->buildFromSearchObjects($searchVariables);
            $metaConfiguration->setUrl($url);
            
            // save this new configuration
            $metaConfigurationService->save($metaConfiguration);
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
            : $this->generateUrl('admin_homepage_metas_index');
        
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