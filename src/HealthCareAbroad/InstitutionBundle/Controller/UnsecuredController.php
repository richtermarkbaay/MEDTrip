<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This controller will not go through security and login checks. 
 * Usually you will use this controller for non-secured transactions thru AJAX request.
 * Requests that will add/modify/delete data should not be put here.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class UnsecuredController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId'));
        $this->institutionService = $this->get('services.institution');
        
        if (!$this->institution) {
            throw $this->createNotFoundException("Invalid institution");
        }
    }
    
    /**
     * Load tabbed contents of an institution
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMultipleCenterInstitutionTabbedContentsAction()
    {
        $output = array();
        $parameters = array('institution' => $this->institution);
        $content = $this->request->get('content', null);
        switch ($content) {
            case 'services':
                $ancillaryServicesData = array(
                                'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                                'selectedAncillaryServices' => array()
                );
                
                foreach ($this->get('services.institution')->getInstitutionServices($this->institution) as $_selectedService) {
                    $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService['id'];
                }
                $parameters['services'] = $ancillaryServicesData;
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig', $parameters));
                break;
            case 'awards':
                $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
                $currentGlobalAwards = $this->institutionService->getGroupedGlobalAwardsByType($this->institution);
                $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
                
                $parameters['form'] = $form->createView();
                $parameters['isSingleCenter'] = $this->institutionService->isSingleCenter($this->institution);
                $parameters['awardsSourceJSON'] = \json_encode($autocompleteSource['award']);
                $parameters['certificatesSourceJSON'] = \json_encode($autocompleteSource['certificate']);
                $parameters['affiliationsSourceJSON'] = \json_encode($autocompleteSource['affiliation']);
                $parameters['accreditationsSourceJSON'] = \json_encode($autocompleteSource['accreditation']);
                $parameters['currentGlobalAwards'] = $currentGlobalAwards;
                $parameters['institution'] = $this->institution;
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Institution/Widgets:institutionAwards.html.twig', $parameters));
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    
    public function loadMedicalCenterTabbedContentsAction()
    {
        return new Response();    
    }
    
    /**
     * Load available global awards of an institution. Used in autocomplete fields
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGlobalAwardSourceAction(Request $request)
    {
        $term = \trim($request->get('term', ''));
        $type = $request->get('type', null);
        $types = \array_flip(GlobalAwardTypes::getTypeKeys());
        $type = \array_key_exists($type, $types) ? $types[$type] : 0;

        $output = array();
        $options = new QueryOptionBag();
        $options->add('globalAward.name', $term);
        if ($type) {
            $options->add('globalAward.type', $type);
        }


        $awards = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')
        ->getAvailableGlobalAwardsOfInstitution($this->institution, $options);


        foreach ($awards as $_award) {
            $output[] = array(
                'id' => $_award->getId(),
                'label' => $_award->getName(),
                'awardingBody' => $_award->getAwardingBody()->getName()
            );
        }
    
        return new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
}