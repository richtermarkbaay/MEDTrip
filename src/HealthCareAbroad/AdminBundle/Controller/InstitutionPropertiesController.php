<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

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
    
    public function indexAction()
    {
        $offeredServiceRepository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty');
         
        $offeredServices = $offeredServiceRepository->getAllServicesByInstitution($this->institution);
        return $this->render('AdminBundle:InstitutionProperties:index.html.twig', array(
                        'offeredServices' => $offeredServices,
                        'institution' => $this->institution
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