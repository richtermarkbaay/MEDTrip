<?php 
/**
 * @author Chaztine Blance
 * 
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
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter = null;
    
    /**
     * @var Request
     */
    protected $request;
    
	public function preExecute()
	{
	    $this->institutionService = $this->get('services.institution');
	    if ($this->institutionService->isSingleCenter($this->institution)) {
	        $this->institutionMedicalCenter = $this->institutionService->getFirstMedicalCenter($this->institution);
	    }
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

            $templateVariables['isSingleCenter'] = true;
            
            // set the first active medical center, ideally we should not do this anymore since a single center only has one center, 
            // but technically we don't impose that restriction in our tables so we could have multiple centers even if the institution is a single center type
            $templateVariables['institutionMedicalCenter'] = $this->get('services.institution')->getFirstMedicalCenter($this->institution);

            if(!$templateVariables['institutionMedicalCenter']) {
                return $this->redirect($this->generateUrl('institution_signup_complete_profile'));
            }

            $templateVariables['institutionMedicalCenterForm'] = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $templateVariables['institutionMedicalCenter'])
                ->createView();
            
            // load medical center specializations
            $templateVariables['specializations'] = $this->institutionMedicalCenter->getInstitutionSpecializations();
        }
        else {
            // multiple center institution profile view
            $templateVariables['medicalCenters'] = $this->get('services.institution_medical_center')->getActiveMedicalCenters($this->institution);
        }
        
        return $this->render('InstitutionBundle:Institution:profile.html.twig', $templateVariables);
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
                $parameters['medical_centers'] = $this->get('services.institution_medical_center')->getActiveMedicalCenters($this->institution);
                $output['medicalCenters'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.activeMedicalCenters.html.twig', $parameters));
                break;
            case 'services':
                $parameters['services'] = $this->institution->getInstitutionOfferedServices();
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig', $parameters));
                break;
            case 'awards':
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionAwards.html.twig', $parameters));
                break;
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for loading tabbed contents in institution profile page
     *
     * @param Request $request
     */
    public function loadSingleTabbedContentsAction(Request $request)
    {
        $content = $request->get('content');
        $output = array();
        $parameters = array('institution' => $this->institution);
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        switch ($content) {
            case 'specializations':
                $parameters['specializations'] = $this->institutionMedicalCenter->getInstitutionSpecializations();
                $output['specializations'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterSpecializations.html.twig', $parameters));
                break;
            case 'services':
                $parameters['services'] = $institutionMedicalCenterService->getMedicalCenterServices($this->institutionMedicalCenter);
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterServices.html.twig', $parameters));
                break;
           case 'awards':
                $parameters['awards'] = $this->institutionMedicalCenter->getInstitutionGlobalAwards();
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterAwards.html.twig',$parameters));
                break;
            case 'medical_specialists':
                $parameters['medical_specialists'] = $this->institutionMedicalCenter->getDoctors();
                $output['medical_specialists'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterSpecialists.html.twig',$parameters));
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


                    // Synchronized Institution and Clinic data IF InstitutionType is SINGLE_CENTER
                    if ($this->institution->getType() == InstitutionTypes::SINGLE_CENTER) {
                        $center = $this->get('services.institution')->getFirstMedicalCenter($this->institution);

                        $center->setName($this->institution->getName());
                        $center->setDescription($this->institution->getDescription());
                        $center->setAddress($this->institution->getAddress1());
                        $center->setContactNumber($this->institution->getContactNumber());
                        $center->setContactEmail($this->institution->getContactEmail());
                        $center->setWebsites($this->institution->getWebsites());
                        $center->setDateUpdated($this->institution->getDateModified());

                        $this->get('services.institution_medical_center')->save($center);
                    }
                    
                    $output['institution'] = array();
                    foreach ($formVariables as $key => $v){
                        $value = $this->institution->{'get'.$key}();

                        if(is_object($value)) {
                            $value = $value->__toString();
                        }

                        if($key == 'address1' || $key == 'contactNumber' || $key == 'websites') {
                            $value = json_decode($value, true);
                        }

                        $output['institution'][$key] = $value;
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