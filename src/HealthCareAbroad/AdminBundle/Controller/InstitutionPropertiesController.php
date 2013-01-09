<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionPropertiesController extends Controller
{
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
        $this->institutionService = $this->get('services.institution');
        $this->institution = $this->get('services.institution.factory')->findById($this->getRequest()->get('institutionId', 0));
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
    }
    
    public function indexAction(Request $request)
    {
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selectedAncillaryServices' => array()
        );
        
        foreach ($this->get('services.institution')->getInstitutionServices($this->institution) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService['id'];
        }

        return $this->render('AdminBundle:InstitutionProperties:index.html.twig', array(
                        'services' => $ancillaryServicesData,
                        'institution' => $this->institution
        ));
        
    }
    
    public function viewGlobalAwardsAction(Request $request)
    {
        $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
        $repo = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward');
        $globalAwards = $repo->findBy(array('status' => GlobalAward::STATUS_ACTIVE));
         
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $awardTypes = GlobalAwardTypes::getTypes();
        $currentGlobalAwards = array('award' => array(), 'certificate' => array(), 'affiliation' => array());
        $autocompleteSource = array('award' => array(), 'certificate' => array(), 'affiliation' => array());
         
        // get the current property values
        $currentAwardPropertyValues = $this->get('services.institution')->getPropertyValues($this->institution, $propertyType);
        
        foreach ($currentAwardPropertyValues as $_prop) {
            $_global_award = $repo->find($_prop->getValue());
            if ($_global_award) {
                $currentGlobalAwards[\strtolower($awardTypes[$_global_award->getType()])][] = array(
                                'global_award' => $_global_award,
                                'institution_property' => $_prop
                );
            }
        }
        
        foreach ($globalAwards as $_award) {
            $_arr = array('id' => $_award->getId(), 'label' => $_award->getName());
            $_arr['awardingBody'] = $_award->getAwardingBody()->getName();
            $autocompleteSource[\strtolower($awardTypes[$_award->getType()])][] = $_arr;
        }
        
        return $this->render('AdminBundle:InstitutionGlobalAwards:index.html.twig', array(
                        'form' => $form->createView(),
                        'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
                        'awardsSourceJSON' => \json_encode($autocompleteSource['award']),
                        'certificatesSourceJSON' => \json_encode($autocompleteSource['certificate']),
                        'affiliationsSourceJSON' => \json_encode($autocompleteSource['affiliation']),
                        'currentGlobalAwards' => $currentGlobalAwards,
                        'institution' => $this->institution,
        ));
        
    }
     
    public function addAncilliaryServiceAction(Request $request)
    {
        $offeredServicesArray = $this->getRequest()->get('offeredServicesData');
        if($request->get('imcId')) {
            $center = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
            $imcProperty = $this->get('services.institution_medical_center_property.formFactory')->buildFormByInstitutionMedicalCenterPropertyTypeName($this->institution, $center, 'ancilliary_service_id')->getData();
            $imcProperty->setValue($offeredServicesArray);
            $this->get('services.institution_medical_center_property')->createInstitutionMedicalCenterPropertyByServices($imcProperty);
        }
        else {
            $institutionProperty = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id')->getData();
            $institutionProperty->setValue($offeredServicesArray);
            $this->get('services.institution_property')->createInstitutionPropertyByServices($institutionProperty);
        }
        return $this->_jsonResponse(array('success' => 1));
    }
    
    public function addLanguageSpokenAction(Request $request)
    {
        $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'language_id');
        $formActionUrl = $this->generateUrl('admin_institution_addLanguageSpoken', array('institutionId' => $this->institution->getId()));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->get('services.institution_property')->save($form->getData());
        
                return $this->redirect($formActionUrl);
            }
        }
        
        $params = array(
            'formAction' => $formActionUrl,
            'form' => $form->createView()
        );
        return $this->render('AdminBundle:InstitutionProperties:common.form.html.twig', $params);
    }
    private function _jsonResponse($data=array(), $code=200)
    {
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
}