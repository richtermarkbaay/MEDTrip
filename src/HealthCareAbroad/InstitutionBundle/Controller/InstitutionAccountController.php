<?php
/**
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\MediaBundle\Services\MediaService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\Security\Core\SecurityContext;

use Gaufrette\File;

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
        parent::preExecute();
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');

        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->repository->find($imcId);
        }


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
        //reset for in InstitutionSignUpController signUpAction() this will be temporarily set to uniqid() as a workaround for slug error
        $this->institution->setName('');

        $this->confirmationMessage = '<b>Congratulations!</b> Your account has been successfully created.';

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
     * Action page for Institution Profile Page
     *
     * @param Request $request
     */
    public function profileAction(Request $request)
    {
        $approvedCenters = $this->repository->getInstitutionMedicalCentersByStatusQueryBuilder($this->institution, InstitutionMedicalCenterStatus::APPROVED);
        $pagerAdapter = new DoctrineOrmAdapter($approvedCenters);
        $pagerParams = array(
                        'page' => $request->get('page', 1),
                        'limit' => 10
        );
        $pager = new Pager($pagerAdapter, $pagerParams);
        
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

            $signupStepStatus = $this->institution->getSignupStepStatus();
//             if(!InstitutionSignupStepStatus::hasCompletedSteps($signupStepStatus)) {
//                 $routeName = InstitutionSignupStepStatus::getRouteNameByStatus($signupStepStatus);
//                 $params = InstitutionSignupStepStatus::isStep1($signupStepStatus) ? array() : array('imcId' => $this->institutionMedicalCenter->getId());
//                 return $this->redirect($this->generateUrl($routeName, $params));
//             }
//             else {
//                 if (!$templateVariables['institutionMedicalCenter']) {
//                     // this must have been created from HCA Admin since this single institution does not have a medical center
//                     return $this->redirect($this->generateUrl(InstitutionSignupStepStatus::getRouteNameByStatus(InstitutionSignupStepStatus::STEP1)));
//                 }
//             }

            $templateVariables['institutionMedicalCenterForm'] = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $templateVariables['institutionMedicalCenter'])
                ->createView();

            // load medical center specializations
            $templateVariables['specializations'] = $this->institutionMedicalCenter->getInstitutionSpecializations();
            $templateVariables['commonDeleteForm'] = $this->createForm(new CommonDeleteFormType())->createView();

        }
        else {
            $currentGlobalAwards = $this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);
            $templateVariables =  array(
                            'institution' => $this->institution,
                            'statusList' => InstitutionMedicalCenterStatus::getStatusList(),
                            'pager' => $pager,
                            'institutionForm' => $form->createView(),
                            'ancillaryServicesData' =>  $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            'currentGlobalAwards' => $currentGlobalAwards
            );
        }

        return $this->render('InstitutionBundle:Institution:profile.html.twig', $templateVariables);
    }

    /**
     * Ajax handler for updating institution profile fields.
     *
     * @param Request $request
     * @author acgvelarde
     */
    public function ajaxUpdateProfileByFieldAction(Request $request)
    {
        $propertyService = $this->get('services.institution_property');
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
                    if(!empty($form['services'])){
                          $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
                          $propertyService->removeInstitutionPropertiesByPropertyType($this->institution, $propertyType);
                          $propertyService->addServicesForInstitution($this->institution, $form['services']->getData());
                    }if(!empty($form['awards'])){
                        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
                        $propertyService->removeInstitutionPropertiesByPropertyType($this->institution, $propertyType);
                        $propertyService->addAwardsForInstitution($this->institution, $form['awards']->getData());
                    }
                    
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
                        if($key == 'services'){
                            
                            $html = $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig', array(
                                            'institution' => $this->institution,
                                            'ancillaryServicesData' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            ));
                            
                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
                            
                        }if($key == 'awards'){
                            
                            $html = $this->renderView('InstitutionBundle:Institution/Widgets:institutionAwards.html.twig', array(
                                            'institution' => $this->institution,
                                            'currentGlobalAwards' => $this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution),
                            ));
                            
                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
                        }
                        else{
                            $value = $this->institution->{'get'.$key}();
        
                            if(is_object($value)) {
                                $value = $value->__toString();
                            }
        
                            if($key == 'address1' || $key == 'contactNumber' || $key == 'socialMediaSites') {
                                $value = json_decode($value, true);
                            }
                            $output['institution'][$key] = $value;
                        }
                    }
                    $output['form_error'] = 0;
                }
                else {
                    // construct the error message
                    $html ="<ul class='text-error' style='margin: 0px;'>";
                    foreach ($form->getErrors() as $err){
                         $html .= '<li>'.$err->getMessage().'</li>';
                    }
                    $html .= '</ul>';

                    $output['form_error'] = 1;
                    $output['form_error_html'] = $html;

                    return new Response(\json_encode($output), 400, array('content-type' => 'application/json'));
                }
            }
            catch (\Exception $e) {
                return new Response($e->getMessage(),500);
            }
        }
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }

    /**
     * Ajax handler for updating institution coordinates field.
     *
     * @param Request $request
     * @author Adelbert D. Silla
     */
    public function ajaxUpdateCoordinatesAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->institution->setCoordinates($request->get('coordinates'));
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institution);
            $em->flush($this->institution);

            return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
        }
    }

    /**
     *
     * @param unknown_type $institutionId
     */
    public function addGlobalAwardsAction()
    {
        $form = $this->createForm(new InstitutionGlobalAwardFormType(),$this->institution);

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {

                $this->institution = $this->get('services.institution')
                ->saveAsDraft($form->getData());

                $this->request->getSession()->setFlash('success', "GlobalAward has been saved!");

                return $this->redirect($this->generateUrl('institution_medicalCenter_view',
                                array('institutionId' => $this->institution->getId())));
            }
        }
        return $this->render('AdminBundle:InstitutionTreatments:addGlobalAward.html.twig', array(
                        'form' => $form->createView(),
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'institution' => $this->institution
        ));
    }


    public function ajaxRemovePropertyValueAction(Request $request)
    {
        $property = $this->get('services.institution_property')->findById($request->get('id', 0));

        if (!$property) {
            throw $this->createNotFoundException('Invalid Institution property.');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($property);
        $em->flush();

        return new Response("Property removed", 200);
    }

    /**
     * Upload logo or featuredImage for Institution
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        $fileBag = $request->files;

        $this->get('services.institution.media')->upload($fileBag->get('file'), $this->institution, $request->get('image_type'));

        return $this->redirect($this->generateUrl('institution_account_profile'));
    }
}