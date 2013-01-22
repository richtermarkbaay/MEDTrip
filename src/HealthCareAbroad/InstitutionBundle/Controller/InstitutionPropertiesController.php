<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionPropertiesController extends InstitutionAwareController
{
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function preExecute()
    {
        $this->institutionService = $this->get('services.institution');
        
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
    }
    
//     public function addAncilliaryServiceAction(Request $request)
//     {
//         $offeredServicesArray = $this->getRequest()->get('offeredServicesData');
//         if($request->get('imcId')) {
//             $center = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
//             $imcProperty = $this->get('services.institution_medical_center_property.formFactory')->buildFormByInstitutionMedicalCenterPropertyTypeName($this->institution, $center, 'ancilliary_service_id')->getData();
//             $imcProperty->setValue($offeredServicesArray);
//             $this->get('services.institution_medical_center_property')->createInstitutionMedicalCenterPropertyByServices($imcProperty);
//         }
//         else {
//             $institutionProperty = $this->get('services.institution_property.formFactory')->buildFormByInstitutionPropertyTypeName($this->institution, 'ancilliary_service_id')->getData();
//             $institutionProperty->setValue($offeredServicesArray);
//             $this->get('services.institution_property')->createInstitutionPropertyByServices($institutionProperty);
//         }
//         return $this->_jsonResponse(array('success' => 1));
//     }
    
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
    
                $html = $this->renderView('InstitutionBundle:Institution/Partials:row.globalAward.html.twig', array(
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