<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;
use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;
use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
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
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $this->institutionService = $this->get('services.institution');
        $this->institution = $this->get('services.institution.factory')->findById($this->getRequest()->get('institutionId', 0));
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
        
        // check InstitutionMedicalCenter
        $this->request = $this->getRequest();
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId, false);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }
        
        }
    }
    
    /**
     * View Institution Offered Services
     * @author Chaztine Blance
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selected' => array(),
                        'currentAncillaryData' => array()
        );
        
        foreach ($this->get('services.institution_property')->getInstitutionByPropertyType($this->institution, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                                    'id' => $_selectedService->getId(),
                                    'value' => $_selectedService->getValue(),
                    );
            $ancillaryServicesData['selected'][] = $_selectedService->getValue();
           
        }

        return $this->render('AdminBundle:InstitutionProperties:index.html.twig', array(
                        'ancillaryServicesData' => $ancillaryServicesData,
                        'institution' => $this->institution,
        ));
        
    }
    
    /*
     * Show all ancillaryServices by Institution 
     */
    public function  showInstitutionAncillaryServiceAction(Request $request)
    {
        $assignedServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        
        $institutionAncillaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->getUnAssignedInstitutionServicesToInstitutionMedicalCenter($this->institution, $assignedServices);
        $params = array('institutionServices' => $institutionAncillaryServices);

        $output = array('html' => $this->renderView('AdminBundle:InstitutionProperties/Partials:rowList_institutionServices.html.twig', $params),
                        'count' => count($institutionAncillaryServices));
        
        return $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
    
    /*
     * Show all globalAwards by Institution
    */
    public function showInstitutionGlobalAwardsAction(Request $request)
    {
        $assignedGlobalAwards = $this->get('services.institution_medical_center')->getMedicalCenterGlobalAwards($this->institutionMedicalCenter);
        $availableGlobalAwards = $this->get('services.institution_property')->getUnAssignedInstitutionGlobalAwardsToInstitutionMedicalCenter($this->institution, $assignedGlobalAwards);

        $params = array('globalAwards' => $availableGlobalAwards);
        $output = array('html' => $this->renderView('AdminBundle:InstitutionProperties/Partials:rowList_institutionGlobalAwards.html.twig', $params),
                        'count' => count($availableGlobalAwards));
        
        return $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * copy assigned institution globalAwards to medical center
     * Required parameters:
     *     - institutionId
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author ajacobe
     */
    public function portInstitutionGlobalAwardsAction(Request $request)
    {
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        
        $assignedGlobalAwards = $this->get('services.institution_medical_center')->getMedicalCenterGlobalAwards($this->institutionMedicalCenter);
        $availableGlobalAwards = $this->get('services.institution_property')->getUnAssignedInstitutionGlobalAwardsToInstitutionMedicalCenter($this->institution, $assignedGlobalAwards);
        
        if($request->get('isCopy')) {
            $em = $this->getDoctrine()->getEntityManager();
            foreach ($availableGlobalAwards as $award)
            {
                
                // check if this medical center already have this property value
                if (!$this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $award['id'])) {
    
                    $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
                    $property->setValue($award['id']);
                    $em->persist($property);
                }
            }
            
            try {
                $em->flush();

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }

        return $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId())));
    }
    
    /**
     * Add an ancillary service to institution
     * Required parameters:
     *     - institutionId
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxAddInstitutionAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
        ->find($request->get('id', 0));
    
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        // check if this institution already have this property value
        if ($this->get('services.institution')->hasPropertyValue($this->institution, $propertyType, $ancillaryService->getId())) {
            $response = new Response("Property value {$ancillaryService->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionPropertyByName($propertyType->getName(), $this->institution);
            $property->setValue($ancillaryService->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                $output = array(
                    'label' => 'Delete Service',
                    'href' => $this->generateUrl('admin_institution_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(), 'id' => $property->getId() )),
                    '_isSelected' => true,
                );

                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
    
        }
    
        return $response;
    }
    
    /**
     * Remove an ancillary service from institution
     * Required parameters:
     *     - institutionId
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxRemoveInstitutionAncillaryServiceAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('id', 0));
    
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
    
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($property->getValue());
    
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
    
            // Invalidate Institution Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

            $output = array(
                'label' => 'Add Service',
                'href' => $this->generateUrl('admin_institution_ajaxAddAncillaryService', array('institutionId' => $this->institution->getId(), 'id' => $ancillaryService->getId() )),
                '_isSelected' => false,
            );
    
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
    }
    
    /**
     * Add GlobalAward to Institution
     *
     * @param Request $request
     * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
     */
    public function ajaxAddInstitutionGlobalAwardAction(Request $request)
    {
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id'));
        if (!$award) {
            throw $this->createNotFoundException();
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        // check if this medical center already have this property
        if ($this->get('services.institution')->hasPropertyValue($this->institution, $propertyType, $award->getId())) {
            $response = new Response("Property value {$award->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionPropertyByName(InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institution);
            $property->setValue($award->getId());
            try {
    
                $propertyService->save($property);
    
                $html = $this->renderView('AdminBundle:InstitutionProperties/Partials:row.globalAward.html.twig', array(
                    'institution' => $this->institution,
                    'award' => $award,
                    'property' => $property
                ));

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($request->get('institutionId')));

                $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }
    
        return $response;
    }
    
    /**
     * Remove institution global award
     *
     * @param Request $request
     */
    public function ajaxInstitutionRemoveGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('id', 0));
    
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
    
        $form = $this->createForm(new CommonDeleteFormType(), $property);
    
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
    
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($property);
                $em->flush();

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                $response = new Response(\json_encode(array('id' => $request->get('id', 0))), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
    
        return $response;
    }
    /*
     * Remove institution globalAwards
     */
    public function ajaxEditInstitutionGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->find($request->get('propertyId', 0));
        $propertyType = $this->get('services.institution_property')->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
    
        $globalAward = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('globalAwardId'));
        if (!$globalAward) {
            throw $this->createNotFoundException('Invalid global award.');
        }
        $property->setValueObject($globalAward);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType(), $property);
        if ($request->isMethod('POST')) {
            $editGlobalAwardForm->bind($request);
            if ($editGlobalAwardForm->isValid()) {
                try {
                    $property = $editGlobalAwardForm->getData();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($property);
                    $em->flush();
                    $extraValue = \json_decode($property->getExtraValue(), true);

                    // Invalidate Institution Profile cache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                    $output = array(
                        'targetRow' => '#globalAwardRow_'.$property->getId(),
                        'html' => $property->getExtraValue()
                    );
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Form error'.$e->getMessage(), 400);
            }
        }
    
        return $response;
    }
    
    public function viewGlobalAwardsAction(Request $request)
    {
        $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
        $repo = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward');
        $globalAwards = $repo->findBy(array('status' => GlobalAward::STATUS_ACTIVE));
         
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $awardTypes = GlobalAwardTypes::getTypes();
        $currentGlobalAwards =$this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
    
        return $this->render('AdminBundle:InstitutionGlobalAwards:index.html.twig', array(
                        'form' => $form->createView(),
                        'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
                        'awardsSourceJSON' => \json_encode($autocompleteSource['award']),
                        'certificatesSourceJSON' => \json_encode($autocompleteSource['certificate']),
                        'affiliationsSourceJSON' => \json_encode($autocompleteSource['affiliation']),
                        'accreditationsSourceJSON' => \json_encode($autocompleteSource['accreditation']),
                        'currentGlobalAwards' => $currentGlobalAwards,
                        'institution' => $this->institution,
                        'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
                        'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView()
        ));
    
    }
    
    /*
     * Add Languages Spoken to Institution
     */
    public function addInstitutionLanguageSpokenAction(Request $request)
    {
        $form = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'language_id');
        $formActionUrl = $this->generateUrl('admin_institution_addLanguageSpoken', array('institutionId' => $this->institution->getId()));
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->get('services.institution_property')->save($form->getData());

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                return $this->redirect($formActionUrl);
            }
        }
    
        $params = array(
            'formAction' => $formActionUrl,
            'form' => $form->createView()
        );
        return $this->render('AdminBundle:InstitutionProperties:common.form.html.twig', $params);
    }
    
    /**
     * Add an ancillary service to institution medical center
     * Required parameters:
     *     - institutionId
     *     - imcId institution medical center id
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author acgvelarde
     */
    public function ajaxAddInstitutionMedicalCenterAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($request->get('id', 0));
    
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
    
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        // check if this medical center already have this property value
        if ($this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService->getId())) {
            $response = new Response("Property value {$ancillaryService->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
            $property->setValue($ancillaryService->getId());
            // get global,current and selcted ancillary services
            $ancillaryServicesData = array(
                            'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            'selected' => array(),
                            'currentAncillaryData' => array()
            );
            $ancillaryServicesData = $this->get('services.institution_medical_center_property')->getCurrentAndSelectedAncillaryServicesByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $ancillaryServicesData);
    
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));

                $output = array(
                    'label' => 'Delete Service',
                    'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $property->getId() )),
                    'ancillaryServicesData' => $ancillaryServicesData,
                    '_isSelected' => true,
                );
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }
    
        return $response;
    }
    
    /**
     * Remove an ancillary service to institution medical center
     * Required parameters:
     *     - institutionId
     *     - imcId institution medical center id
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author acgvelarde
     */
    public function ajaxRemoveInstitutionMedicalCenterAncillaryServiceAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->find($request->get('id', 0));
    
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
        // get global,current and selcted ancillary services
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($property->getValue());
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selected' => array(),
                        'currentAncillaryData' => array()
        );
        $ancillaryServicesData = $this->get('services.institution_medical_center_property')->getCurrentAndSelectedAncillaryServicesByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $ancillaryServicesData);
    
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
    
            // Invalidate InstitutionMedicalCenter Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));

            $output = array(
                'label' => 'Add Service',
                'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxAddAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $ancillaryService->getId() )),
                'ancillaryServicesData' => $ancillaryServicesData,
                '_isSelected' => false,
            );
    
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
    }
    
    /**
     * copy assigned institution anciallary services to medical center
     * Required parameters:
     *     - institutionId
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author ajacobe
     */
    public function portInstitutionAncillaryServiceAction(Request $request)
    {
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        $assignedServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $institutionAncillaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->getUnAssignedInstitutionServicesToInstitutionMedicalCenter($this->institution, $assignedServices);
    
        if($request->get('isCopy')) {
            foreach ($institutionAncillaryServices as $ancillaryService)
            {
                // check if this medical center already have this property value
                if (!$this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService['id'])) {
    
                    $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
                    $property->setValue($ancillaryService['id']);
                    try {
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($property);
                        $em->flush();

                        // Invalidate InstitutionMedicalCenter Profile cache
                        $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));
                    }
                    catch (\Exception $e){
                        $response = new Response($e->getMessage(), 500);
                    }
                }
            }
        }
    
        // get global ancillary services
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selectedAncillaryServices' => array()
        );
        foreach ($this->get('services.institution_medical_center_property')->getInstitutionMedicalCenterByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                            'id' => $_selectedService->getId(),
                            'value' => $_selectedService->getValue(),
            );
            $ancillaryServicesData['selected'][] = $_selectedService->getValue();
        }
        
        foreach ($this->get('services.institution_medical_center')->getMedicalCenterServices($this->institutionMedicalCenter) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService->getId();
        }
    
        $output = array(
            'html' => $this->renderView('AdminBundle:InstitutionProperties/Partials:view.ancillaryServices.html.twig', array(
                'institution' => $this->institution,
                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                'ancillaryServicesData' => $ancillaryServicesData,
                '_isSelected' => true
            )),
            'error' => 0
        );

        return $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }

    /*
     * Ajax Handler that remove InstitutionMedicalCenter GlobalAwards
    */
    public function ajaxRemoveInstitutionMedicalCenterGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->find($request->get('id'));
    
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
    
        $form = $this->createForm(new CommonDeleteFormType(), $property);
    
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
    
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($property);
                $em->flush();

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));

                $response = new Response(\json_encode(array('id' => $request->get('id'))), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
    
        return $response;
    }
    
    
    /*
     * Ajax Handler that edit InstitutionMedicalCenter GlobalAwards
     */
    public function ajaxEditInstitutionMedicalCenterGlobalAwardAction()
    {
        $globalAward = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($this->request->get('globalAwardId', 0));
        if (!$globalAward) {
            throw $this->createNotFoundException('Invalid global award');
        }
        $propertyType = $this->get('services.institution_property')->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $imcProperty = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $globalAward->getId());
        $imcProperty->setValueObject($globalAward);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType(), $imcProperty);
        if ($this->request->isMethod('POST')) {
            $editGlobalAwardForm->bind($this->request);
            if ($editGlobalAwardForm->isValid()) {
                try {
                    $imcProperty = $editGlobalAwardForm->getData();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($imcProperty);
                    $em->flush();

                    // Invalidate InstitutionMedicalCenter Profile cache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));

                    $extraValue = \json_decode($imcProperty->getExtraValue(), true);
                    $output = array(
                        'targetRow' => '#globalAwardRow_'.$imcProperty->getId(),
                        'html' => $imcProperty->getExtraValue()
                    );
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Form error'.$e->getMessage(), 400);
            }
        }

        return $response;
    }
    
    /*
     * Ajax Handler that add Global Awards to InstitutionMedicalCenter
     */
    public function ajaxAddInstitutionMedicalCenterGlobalAwardAction(Request $request)
    {
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id'));
    
        if (!$award) {
            throw $this->createNotFoundException();
        }
    
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        // check if this medical center already have this property
        if ($this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $award->getId())) {
            $response = new Response("Award {$award->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
            $property->setValue($award->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($request->get('imcId')));

                $html = $this->renderView('AdminBundle:InstitutionMedicalCenterProperties/Partials:row.globalAward.html.twig', array(
                    'award' => $award,
                    'property' => $property,
                    'institution' => $this->institution,
                    'institutionMedicalCenter' => $this->institutionMedicalCenter,
                    'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
                ));
    
                $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }
    
        return $response;
    }
    
    private function _jsonResponse($data=array(), $code=200)
    {
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
    
}