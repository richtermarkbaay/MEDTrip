<?php
/**
 * DEPRECATED ???
 * Note added by: Adelbert Silla
 * 
 */  
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

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
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId'));
        $this->institutionService = $this->get('services.institution');
        
        if (!$this->institution) {
            throw $this->createNotFoundException("Invalid institution");
        }
        
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')
                ->findById($imcId);
            // institution medical center does not belong to institution
            if ($this->institutionMedicalCenter && $this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                
                return new Response('Medical center does not belong to institution', 401);
            }
        }
    }
    
    /**
     * DEPRECATED ??? - Note added by: Adelbert
     *   
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
                    'currentAncillaryData' => array(),
                    'selected' => array()
                );
                
                foreach ($this->get('services.institution_property')->getInstitutionByPropertyType($this->institution, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
                    $ancillaryServicesData['currentAncillaryData'][] = array(
                                    'id' => $_selectedService->getId(),
                                    'value' => $_selectedService->getValue(),
                    );
                    $ancillaryServicesData['selected'][] = $_selectedService->getValue();
                }
                
                $parameters['services'] = $ancillaryServicesData;
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig', $parameters));
                break;
            case 'awards':
                $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
                //$currentGlobalAwards = $this->institutionService->getGroupedGlobalAwardsByType($this->institution);
                $currentGlobalAwards = $this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);
                $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
                $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
                
                $parameters['form'] = $form->createView();
                $parameters['editGlobalAwardForm'] = $editGlobalAwardForm->createView();
                $parameters['isSingleCenter'] = $this->institutionService->isSingleCenter($this->institution);
                $parameters['awardsSourceJSON'] = \json_encode($autocompleteSource['award']);
                $parameters['certificatesSourceJSON'] = \json_encode($autocompleteSource['certificate']);
                $parameters['affiliationsSourceJSON'] = \json_encode($autocompleteSource['affiliation']);
                $parameters['accreditationsSourceJSON'] = \json_encode($autocompleteSource['accreditation']);
                $parameters['currentGlobalAwards'] = $currentGlobalAwards;
                $parameters['institution'] = $this->institution;
                $parameters['commonDeleteForm'] = $this->createForm(new CommonDeleteFormType())->createView();
                $output['awards'] = array('html' => '');
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
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