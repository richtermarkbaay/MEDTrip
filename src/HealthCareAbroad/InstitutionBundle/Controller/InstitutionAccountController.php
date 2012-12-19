<?php 
/**
 * @author Chaztine Blance
 * Create Profile after Sign up
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Services\SignUpService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\Security\Core\SecurityContext;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;

class InstitutionAccountController extends InstitutionAwareController
{
    /**
     * @var InstitutionService
     */
    protected $institutionService;
    
    /**
     * @var Request
     */
    protected $request;
    
	public function preExecute()
	{
	    $this->institutionService = $this->get('services.institution');
	    $this->request = $this->getRequest();
	}
	
	/**
	 * Landing page after signing up as an Institution. Logic will differ depending on the type of institution
	 * 
	 * @param Request $request
	 */
    public function completeProfileAfterRegistrationAction(Request $request)
    {
        switch ($this->institution->getType())
        {
            case InstitutionTypes::SINGLE_CENTER:
                $response = $this->completeRegistrationSingleCenter();
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
            case InstitutionTypes::MEDICAL_TOURISM_FACILITATOR:
            default:
                $response = $this->completeRegistrationMultipleCenter();
                break;
        }

        return $response;
    }
    
    public function addMedicalSpecialistAction(Request $request)
    {
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->findAll();
        $form = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
        if ($request->isMethod('POST')) {
        
            $form->bind($request);
            if ($form->isValid()) {
        
                $institution = $this->get('services.institution.factory')->save($form->getData());
                $this->get('session')->setFlash('notice', "Successfully updated Languages Spoken");
        
                //create event on editInstitution and dispatch
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
                return $this->redirect($this->generateUrl('admin_institution_edit', array('institutionId' => $this->institution->getId())));
            }
        }
        $doctorArr = array();
        foreach ($doctors as $e) {
        
            $doctorArr[] = array('value' => $e->getFirstName() ." ". $e->getLastName(), 'id' => $e->getId());
        }
        
        return $this->render('InstitutionBundle:Institution:add.medicalSpecialist.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'doctorsJSON' => \json_encode($doctorArr)
        ));
    }
    
    /*
     * Get doctors list that is not assigned to Institution
    */
    public function searchMedicalSpecialistAction(Request $request)
    {
        $searchTerm = $request->get('name_startsWith');
        $data = array();
        $doctors = $this->getDoctrine()->getRepository("DoctorBundle:Doctor")->getDoctorsBySearchTerm($searchTerm, $this->institution->getId());
    
        foreach($doctors as $each) {
            $data[] = array('id' => $each->getId(),
                            'firstName' => $each->getFirstName(),
                            'middleName' => $each->getMiddleName(),
                            'lastName' => $each->getLastName());
        }
    
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    public function addServiceAction(Request $request)
    {
        $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id');
   	    $formActionUrl = $this->generateUrl('institution_addAncilliaryService', array('institutionId' => $this->institution->getId()));
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
        return $this->render('InstitutionBundle:Institution:add.services.html.twig', $params);
    }
    /**
     * This is the action handler after signing up as an Institution with Single Center.
     * User will be directed immediately to create clinic page.
     * 
     * TODO:
     *     This has a crappy rule where institution name and description will internally be the name and description of the clinic.
     *     
     * @author acgvelarde    
     * @return
     */
    protected function completeRegistrationSingleCenter()
    {
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
        $institutionService = $this->get('services.institution');
        if (!$institutionService->isSingleCenter($this->institution)) {
            // this is not a single center institution, where will we redirect it? for now let us redirect it to dashboard
            
            return $this->redirect($this->generateUrl('institution_homepage'));
        }
        $institutionMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);
        if (\is_null($institutionMedicalCenter)) {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
        }
        
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            
            if ($form->isValid()) {
                    
                // save institution and create an institution medical center
                $this->get('services.institution_signup')
                    ->completeProfileOfInstitutionWithSingleCenter($form->getData(), $institutionMedicalCenter);
                
                // this should redirect to 2nd step
                return $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations', array('imcId' => $institutionMedicalCenter->getId())));
            }
        }
        
        return $this->render('InstitutionBundle:Institution:afterRegistration.singleCenter.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'isSingleCenter' => true
        ));
    }
    
    /**
     * 
     */
    protected function completeRegistrationMultipleCenter()
    {
        $hiddenFields = array('name', 'description');
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_HIDDEN_FIELDS => $hiddenFields));
        $institutionTypeLabels = InstitutionTypes::getLabelList();
        
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            
            if ($form->isValid()) {
                
                $this->get('services.institution_signup')
                    ->completeProfileOfInstitutionWithMultipleCenter($form->getData());
                
                return $this->redirect($this->generateUrl('institution_homepage'));
            }
        }
        
        return $this->render('InstitutionBundle:Institution:afterRegistration.multipleCenter.html.twig', array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'hiddenFields' => $hiddenFields,
            'institutionTypeLabel' => $institutionTypeLabels[$this->institution->getType()]
        ));
    }
    
    /**
     * Action page for Institution Profile Page
     * 
     * @param Request $request
     */
    public function profileAction(Request $request)
    {
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution);
        $templateVariables = array(
            'institutionForm' => $form->createView(),
            'institution' => $this->institution
        );
        if (InstitutionTypes::SINGLE_CENTER == $this->institution->getType()) {
            
            // set the first active medical center, ideally we should not do this anymore since a single center only has one center, 
            // but technically we don't impose that restriction in our tables so we could have multiple centers even if the institution is a single center type
            $templateVariables['institutionMedicalCenter'] = $this->get('services.institution')->getFirstMedicalCenter($this->institution);
            $templateVariables['institutionMedicalCenterForm'] = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $templateVariables['institutionMedicalCenter'])
                ->createView();
            $template = 'InstitutionBundle:Institution:profile.singleCenter.html.twig';
        }
        else {
            $template = 'InstitutionBundle:Institution:profile.multipleCenter.html.twig';
        }
        
//         $institutionSpecializations = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getByInstitutionMedicalCenter($institutionMedicalCenter);
        
        return $this->render($template, $templateVariables);
    }
    

    /**
     * Ajax handler for loading tabbed contents in institution profile page
     * 
     * @param Request $request
     */
    public function loadTabbedContentsAction(Request $request)
    {
        $content = $request->get('content');
        $output = array();
        $parameters = array('institution' => $this->institution);
        switch ($content) {
            case 'medical_centers':
                $output['medicalCenters'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.activeMedicalCenters.html.twig', $parameters));
                break;
            case 'services':
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig'));
                break;
            case 'awards':
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionAwards.html.twig'));
                break;
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for updating institution profile fields.
     * 
     * @param Request $request
     * @author acgvelarde
     */
    public function ajaxUpdateProfileByFieldAction(Request $request)
    {
        $output = array();
        
        if ($request->isMethod('POST')) {
            
            try {
                // set all other fields except those passed as hidden
                $formVariables = $request->get(InstitutionProfileFormType::NAME);
                unset($formVariables['_token']);
                $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));
                
                $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => true, InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));
                
                $form->bind($request);
                if ($form->isValid()) {
                    $this->institution = $form->getData();
                    $this->get('services.institution.factory')->save($this->institution);
                    
                    $output['institution'] = array();
                    foreach ($formVariables as $key => $v){
                        $output['institution'][$key] = $this->institution->{'get'.$key}();
                    }
                    $output['form_error'] = 0;
                }
                else {
                    // construct the error message
                    $html ="<ul class='errors'>";
                    foreach ($form->getErrors() as $err){
                         $html .= '<li>'.$err->getMessage().'</li>';
                    }
                    $html .= '</ul>';
                     
                    //var_dump($form->get('name')->getErrors()); exit;
                    $output['form_error'] = 1;
                    $output['form_error_html'] = $html;
                }    
            }
            catch (\Exception $e) {
                
                return new Response($e->getMessage(),500);
            }
            
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
}