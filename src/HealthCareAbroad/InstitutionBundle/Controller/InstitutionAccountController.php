<?php
/**
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

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
        
        // NOTE: This code is not necessary anymore and can be remove.
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

        $params = array(
            'institutionForm' => $form->createView(),
            'currentGlobalAwards' => $currentGlobalAwards,
            'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
            'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr),
            'ancillaryServicesData' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices()
        );

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

            $editMedicalCenterForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);
            $institutionMedicalCenterForm = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

            $params['editForm'] = $editMedicalCenterForm->createView();
            $params['institutionMedicalCenter'] = $this->institutionMedicalCenter;
            $params['institutionMedicalCenterForm'] = $institutionMedicalCenterForm->createView();
            $params['commonDeleteForm'] = $this->createForm(new CommonDeleteFormType())->createView();
            $params['specializations'] = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitutionMedicalCenter($this->institutionMedicalCenter);
            $params['doctors'] =  $this->get('services.doctor')->doctorsObjectToArray($this->institutionMedicalCenter->getDoctors());
            $params['doctorForm'] = $doctorForm->createView();
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
        if ($request->isMethod('POST')) {
            try {
                $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE));
                $formVariables = $request->get(InstitutionProfileFormType::NAME);
                unset($formVariables['_token']);

                $removedFields = \array_diff(InstitutionProfileFormType::getFieldNames(), array_keys($formVariables));

                $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false, InstitutionProfileFormType::OPTION_REMOVED_FIELDS => $removedFields));

                $formRequestData = $request->get($form->getName());

                // we always expect 1 medical provider group. if it is empty remove it from the array
                if (isset($formRequestData['medicalProviderGroups']) && !empty($formRequestData['medicalProviderGroups']) ) {
                    $providerGroup = trim($formRequestData['medicalProviderGroups'][0]);  
                    if ($providerGroup == '') { 
                        unset($formRequestData['medicalProviderGroups']);
                    } else {
                        $formRequestData['medicalProviderGroups'][0] = str_replace(array("\\'", '\\"'), array("'", '"'), $providerGroup);
                    }
                }

                $form->bind($formRequestData);
                
                if ($form->isValid()) {
                    $propertyService = $this->get('services.institution_property');
                    if(isset($form['services'])) {
                        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
                        $propertyService->removeInstitutionPropertiesByPropertyType($this->institution, $propertyType);
                        $propertyService->addServicesForInstitution($this->institution, $form['services']->getData());                        
                        $responseContent = array('services' => $form['services']);

                    } else if($awardType = $request->get('awardTypeKey')) {

                        $awardsData = isset($form['awards']) ? $form['awards']->getData() : array(); 
                        $propertyService->addAwardsForInstitution($this->institution, $awardsData);
                        $globalAwards = $propertyService->getGlobalAwardPropertiesByInstitution($this->institution);

                        $responseContent['awardsType'] = $awardType;
                        $responseContent['awardsHtml'] = $this->renderView('InstitutionBundle:Widgets/Profile:globalAwards.html.twig', array(
                            'eachAward' => array('list' => $globalAwards[$awardType]),
                            'type' => $awardType.'s',
                            'toggleBtnId' => 'institution-edit-'.$awardType.'s-btn'
                        ));

                    } else {
                        $this->institution = $form->getData();
                        $this->get('services.contact_detail')->removeInvalidContactDetails($this->institution);
                        $this->get('services.institution.factory')->save($this->institution);
                        
                        // Synchronized Institution and Clinic data IF InstitutionType is SINGLE_CENTER
                        if ($this->isSingleCenter) {
                            $this->syncInstitutionDataToClinicData($this->institution);
                        }

                        unset($formRequestData['_token']);
 
                        if(isset($formRequestData['address1'])) {
                            $formRequestData['stringAddress'] = $this->get('services.miscellaneous.twig.extension')->formatInstitutionAddressToString($this->institution);                            
                        }

                        if(isset($formRequestData['contactDetails'])) {
                            $formRequestData['contactDetails'] = $this->get('services.contact_detail')->getContactDetailsStringValue($this->institution->getContactDetails());
                        }

                        $responseContent = array('institution' => $formRequestData);
                    }

                    $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));

                } else {
                    $errors = array();
                    foreach ($form->getChildren() as $field){
                        if (\count($eachErrors = $field->getErrors())){
                            $errors[] = array('field' => $field->getName(), 'error' => $eachErrors[0]->getMessageTemplate());
                        }
                    }

                    $response = new Response(\json_encode(array('errors' => $errors)), 400, array('content-type' => 'application/json'));
                }
            }
            catch (\Exception $e) {
                return new Response($e->getMessage(),500);
            }
        }

        return $response;
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
     * @deprecated
     * Upload logo or featuredImage for Institution
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        $fileBag = $request->files;

        $this->get('services.institution.media')->upload($fileBag->get('file'), $this->institution, $request->get('image_type'));

        return $this->redirect($this->generateUrl('institution_account_profile'));
    }

    /**
     * Synchronized Institution Data to Clinic Data 
     * @param Institution $institution
     */
    private function syncInstitutionDataToClinicData(Institution $institution)
    {
        $center = $this->get('services.institution')->getFirstMedicalCenter($institution);
        $center->setName($institution->getName());
        $center->setDescription($institution->getDescription());
        $center->setAddress($institution->getAddress1());
        $center->setContactEmail($institution->getContactEmail());
        $center->setWebsites($institution->getWebsites());
        $center->setDateUpdated($institution->getDateModified());
        $this->get('services.institution_medical_center')->save($center);        
    }
}