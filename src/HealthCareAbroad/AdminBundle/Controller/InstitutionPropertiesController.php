<?php
namespace HealthCareAbroad\AdminBundle\Controller;

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
    
    public function preExecute()
    {
        $this->institutionService = $this->get('services.institution');
        $this->institution = $this->get('services.institution.factory')->findById($this->getRequest()->get('institutionId', 0));
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
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
                        'services' => $ancillaryServicesData,
                        'institution' => $this->institution,
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
        $currentGlobalAwards =$this->get('services.institution_property')->getGlobalAwardPropertiesByInstitution($this->institution);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
        // get the current property values
        $currentAwardPropertyValues = $this->get('services.institution')->getPropertyValues($this->institution, $propertyType);
        
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
    public function ajaxAddAncillaryServiceAction(Request $request)
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
    
                 $output = array(
                    'label' => 'Delete Service',
                    'href' => $this->generateUrl('admin_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(), 'id' => $property->getId() )),
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
     * Remove an ancillary service to institution
     * Required parameters:
     *     - institutionId
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxRemoveAncillaryServiceAction(Request $request)
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
            
            $output = array(
                    'label' => 'Add Service',
                    'href' => $this->generateUrl('admin_institution_ajaxaddAncilliaryService', array('institutionId' => $this->institution->getId(), 'id' => $ancillaryService->getId() )),
                    '_isSelected' => false,
            );
            
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
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
    
    /**
     * Add a GlobalAward
     *
     * @param Request $request
     * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
     */
    public function ajaxAddGlobalAwardAction(Request $request)
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
                
                $html = $this->renderView('AdminBundle:Institution/Partials:row.globalAwards.html.twig', array(
                    'institution' => $this->institution,
                    'award' => $award,
                    'property' => $property
                ));
    
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
    public function ajaxRemoveGlobalAwardAction(Request $request)
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
        
                $response = new Response(\json_encode(array('id' => $request->get('id', 0))), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
        
        return $response;
    }
    
    public function ajaxEditGlobalAwardAction(Request $request)
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
                    $yearAcquired = \implode(', ',$extraValue[InstitutionGlobalAwardExtraValueDataTransformer::YEAR_ACQUIRED_JSON_KEY]);
                    
                    $output = array(
                                    'targetRow' => '#globalAwardRow_'.$property->getId(),
                                    'html' => $yearAcquired
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
}