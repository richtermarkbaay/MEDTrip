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
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.services.html.twig'));
                break;
            case 'awards':
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.awards.html.twig'));
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