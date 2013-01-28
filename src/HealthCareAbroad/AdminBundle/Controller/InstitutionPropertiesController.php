<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;
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
    
    public function indexAction(Request $request)
    {
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selectedAncillaryServices' => array()
        );
        
        foreach ($this->get('services.institution')->getInstitutionServices($this->institution) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService['id'];
        }

        return $this->render('AdminBundle:InstitutionProperties:index.html.twig', array(
                        'services' => $ancillaryServicesData,
                        'institution' => $this->institution
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
        $currentGlobalAwards =$this->get('services.institution')->getGroupedGlobalAwardsByType($this->institution);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
         
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
     * @author Chaztin Blance
     */
    public function ajaxAddAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
        ->find($request->get('asId', 0));
    
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
                                'html' => $this->renderView('AdminBundle:InstitutionProperties:row.ancillaryService.html.twig', array(
                                                'institution' => $this->institution,
                                                'ancillaryService' => $ancillaryService,
                                                '_isSelected' => true
                                )),
                                'error' => 0
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
     * @author chaztine Blance
     */
    public function ajaxRemoveAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
        ->find($request->get('asId', 0));
    
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        // get property value for this ancillary service
        $property = $this->get('services.institution')->getPropertyValue($this->institution, $propertyType, $ancillaryService->getId());
    
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
    
            $output = array(
                            'html' => $this->renderView('AdminBundle:InstitutionProperties:row.ancillaryService.html.twig', array(
                                            'institution' => $this->institution,
                                            'ancillaryService' => $ancillaryService,
                                            '_isSelected' => false
                            )),
                            'error' => 0
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
            $property = $propertyService->createInstitutionPropertyByName($propertyType->getName(), $this->institution);
            $property->setValue($award->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();
    
                $html = $this->renderView('AdminBundle:Institution/Partials:row.globalAwards.html.twig', array(
                                'institution' => $this->institution,
                                'award' => $award
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
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id', 0));
    
        if (!$award) {
            throw $this->createNotFoundException();
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        // get property value for this ancillary service
        $property = $this->get('services.institution')->getPropertyValue($this->institution, $propertyType, $award->getId());
    
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
    
            $response = new Response('Award property removed', 200);
        }
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
    }
}