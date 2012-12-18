<?php 
/**
 * @author Chaztine Blance
 * Create Profile after Sign up
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

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
                var_dump($form->getData());exit;
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
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            
            if ($form->isValid()) {
                    
                // save institution and create an institution medical center
                $this->get('services.institution_signup')
                    ->completeProfileOfInstitutionWithSingleCenter($form->getData(), $institutionMedicalCenter);
                
                // this should redirect to 2nd step
                return $this->redirect($this->generateUrl('institution_homepage'));
            }
        }
        
        return $this->render('InstitutionBundle:Institution:afterRegistration.singleCenter.html.twig', array(
            'form' => $form->createView(),
            'institutionSpecializations' => $institutionSpecializations,
            'institutionMedicalCenter' => $institutionMedicalCenter
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
        if (InstitutionTypes::SINGLE_CENTER == $this->institution->getType()) {
            $template = 'InstitutionBundle:Institution:profile.singleCenter.html.twig';
        }
        else {
            $template = 'InstitutionBundle:Institution:profile.multipleCenter.html.twig';
        }
        
//         $institutionSpecializations = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getByInstitutionMedicalCenter($institutionMedicalCenter);
        
        echo "<pre>";
        print_r($this->institution);
        echo "</pre>";
        exit;
        
        return $this->render($template, array(
            'institution' => $this->institution
        ));
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
        switch ($content) {
            case 'medical_centers':
                $output['medicalCenters'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.activeMedicalCenters.html.twig'));
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
     * Action page for Institution Profile Page Service Tab
     *
     * @param Request $request
     */
    public function profileServiceAction(Request $request)
    {
            $template = 'InstitutionBundle:Institution:profile.singleCenterServices.html.twig';
    
        return $this->render($template, array(
                        'institution' => $this->institution
        ));
    }
    
    /**
     * Action page for Institution Profile Page Awards Tab
     *
     * @param Request $request
     */
    public function profileAwardsAction(Request $request)
    {
        $template = 'InstitutionBundle:Institution:profile.singleCenterAwards.html.twig';
    
        return $this->render($template, array(
                        'institution' => $this->institution
        ));
    }
    
    /**
     * Action page for Institution Profile Page Specialist Tab
     *
     * @param Request $request
     */
    public function profileSpecialistAction(Request $request)
    {
        $template = 'InstitutionBundle:Institution:profile.singleCenterSpecialist.html.twig';
    
        return $this->render($template, array(
                        'institution' => $this->institution
        ));
    }
}