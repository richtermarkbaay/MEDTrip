<?php
/**
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterDoctorFormType;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

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
        $this->institutionService = $this->get('services.institution');
        $this->request = $this->getRequest();
        
        // NOTE: This code is not neccessary anymore and can be remove.
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->repository->find($imcId);
        }
        // End of note
        
        if ($this->isSingleCenter) {
            $this->institutionMedicalCenter = $this->institutionService->getFirstMedicalCenter($this->institution);
        }
    }

    public function ajaxAddInstitutionUserAction(Request $request)
    {
        
    }
    
    /**
     * Action page for Institution Profile Page
     *
     * @param Request $request
     */
    public function profileAction(Request $request)
    {
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
        $medicalProviderGroupArr = array();
        
        foreach ($medicalProviderGroup as $e) {
            $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE)); 
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));
        $currentGlobalAwards = $this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
        if ($this->isSingleCenter) {
            
            $doctor = new Doctor();
            $doctor->addInstitutionMedicalCenter($this->institutionMedicalCenter);
            $doctorForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);
            $editDoctor = new Doctor();
            if($this->institutionMedicalCenter->getDoctors()->count()) {
                $editDoctor = $this->institutionMedicalCenter->getDoctors()->first();
            }
            if(!$editDoctor->getContactDetails()->count()) {
                $contactDetail = new ContactDetail();
                $editDoctor->addContactDetail($contactDetail);
            }
            
            $editForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);
            
            $institutionMedicalCenterForm = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));
            
            $params['institutionMedicalCenterForm'] = $institutionMedicalCenterForm->createView();
            $params['isSingleCenter'] = true;            
            $params['institutionMedicalCenter'] = $this->institutionMedicalCenter;
            $params['institution'] = $this->institution;
            $params['institutionForm'] = $form->createView();
            $params['specializations'] = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitutionMedicalCenter($this->institutionMedicalCenter);
            $params['medicalProvidersJSON'] = \json_encode($medicalProviderGroupArr);
            $params['currentGlobalAwards'] = $currentGlobalAwards;
            $params['editGlobalAwardForm'] = $editGlobalAwardForm->createView();
            $params['commonDeleteForm'] = $this->createForm(new CommonDeleteFormType())->createView();
            $params['ancillaryServicesData'] = $this->get('services.helper.ancillary_service')->getActiveAncillaryServices();
            $params['doctors'] =  $this->get('services.doctor')->doctorsObjectToArray($this->institutionMedicalCenter->getDoctors());
            $params['doctorForm'] = $doctorForm->createView();
            $params['editForm'] = $editForm->createView();

        } else {
            $params =  array(
                'statusList' => InstitutionMedicalCenterStatus::getStatusList(),
                'institutionForm' => $form->createView(),
                'currentGlobalAwards' => $currentGlobalAwards,
                'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
                'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr),
                'ancillaryServicesData' =>  $this->get('services.helper.ancillary_service')->getActiveAncillaryServices()
            );
        }

        return $this->render('InstitutionBundle:Institution:profile.html.twig', $params);
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
                $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE));
                
                $formVariables = $request->get(InstitutionProfileFormType::NAME);
//                 var_dump($formVariables); exit;
                unset($formVariables['_token']);
                $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));
                $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false, InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));
                
                $formRequestData = $request->get($form->getName());
                if (isset($formRequestData['medicalProviderGroups']) ) {
                    // we always expect 1 medical provider group
                    // if it is empty remove it from the array
                    if (isset($formRequestData['medicalProviderGroups'][0]) && '' == trim($formRequestData['medicalProviderGroups'][0]) ) {
                        unset($formRequestData['medicalProviderGroups'][0]);
                    }
                } 
               
                $form->bind($formRequestData);
                
                if ($form->isValid()) {
                    $this->institution = $form->getData();
                    
                       $this->get('services.contact_detail')->removeInvalidContactDetails($this->institution);
                    $this->get('services.institution.factory')->save($this->institution);
                    if(!empty($form['services'])){
                          $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
                          $propertyService->removeInstitutionPropertiesByPropertyType($this->institution, $propertyType);
                          $propertyService->addServicesForInstitution($this->institution, $form['services']->getData());
                    }if(!empty($form['awards'])){
                        $propertyService->addAwardsForInstitution($this->institution, $form['awards']->getData());
                    }
                    
                    // Synchronized Institution and Clinic data IF InstitutionType is SINGLE_CENTER
                    if ($this->institution->getType() == InstitutionTypes::SINGLE_CENTER) {
                        $center = $this->get('services.institution')->getFirstMedicalCenter($this->institution);
                        $center->setName($this->institution->getName());
                        $center->setDescription($this->institution->getDescription());
                        $center->setAddress($this->institution->getAddress1());
                        //$center->setContactNumber($this->institution->getContactNumber());
                        $center->setContactEmail($this->institution->getContactEmail());
                        $center->setWebsites($this->institution->getWebsites());
                        $center->setDateUpdated($this->institution->getDateModified());

                        $this->get('services.institution_medical_center')->save($center);
                    }
                    $output['institution'] = array();
                    foreach ($formVariables as $key => $v){
                        if($key == 'services'){

                            $html = $this->renderView('InstitutionBundle:Widgets/Profile:services.html.twig', array(
                                'institution' => $this->institution,
                                'ancillaryServicesData' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices()
                            ));
                            
                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
                            
                        }elseif($key == 'awards') {
                            $html = array();
                            $typeKey = $request->get('awardTypeKey');
                            $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
                            $globalAwards = $this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);

                            foreach($globalAwards as $key => $global ) {
                                $html['html'][$key][] = $this->renderView('InstitutionBundle:Widgets/Profile:globalAwards.html.twig', array(
                                    'institution' => $this->institution,
                                    'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
                                    'eachAward' => array('list' => $global),
                                    'type' => $key.'s',
                                    'toggleBtnId' => 'institution-edit-'.$key.'s-btn'
                            ));
                          }
                             
                            return new Response(\json_encode($html), 200, array('content-type' => 'application/json'));
                        }
                        elseif($key == 'medicalProviderGroups' ){
                            $value = $this->institution->{'get'.$key}();
                            $returnVal = ($value[0] != null ? $value[0]->getName() : '' );
                        
                            $output['institution'][$key] = $returnVal;
                        }
                        elseif($key == 'contactDetails' ){
                            $value = $this->get('services.contact_detail')->getContactDetailsStringValue($this->institution->{'get'.$key}());
                            $output['institution'][$key]['phoneNumber'] = $value;
                        }
                        elseif($key == 'address1') {
                            $value = $this->institution->{'get'.$key}();
                            $value = json_decode($value, true);
                            $output['institution'][$key] = $value;
                        }
                        elseif( $key == 'socialMediaSites') {
                            $value = $this->institution->{'get'.$key}();
                            $value = json_decode($value, true);
                            $output['institution'][$key] = $value;
                        }
                        elseif($key == 'country') {
                            $output['institution'][$key] = $this->institution->getCountry()->getName();
                        }
                        elseif( $key == 'city' ) {
                            $output['institution'][$key] = $this->institution->getCity()->getName();
                        }
                       else{  
                           $value = $this->institution->{'get'.$key}();
                           $output['institution'][$key] = $value;
                        }
                    }
                    
                    $output['form_error'] = 0;
                    
                    /*
                     * TODO: Needs to change the validation for awards and services
                     * Always expects empty if form submitted are from awards or services
                     */
                    if(empty($output['institution'])){ 
                        $errors = array('error' => 'Please select at least one.');
                        return new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
                    }
                }
                else {
                    $errors = array();
                    $formErrors = $this->get('validator')->validate($form);

                    foreach ($formErrors as $err) {
                        $errors[] = array('field' => str_replace('data.','',$err->getPropertyPath()), 'error' => $err->getMessage());
                    }
                    return new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
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