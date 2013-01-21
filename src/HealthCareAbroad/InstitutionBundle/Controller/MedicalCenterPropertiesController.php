<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Controller\InstitutionAwareController;

class MedicalCenterPropertiesController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenter
     */
    protected $institutionMedicalCenter;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    protected $imcService;
    
    /**
     * @var Request
     */
    protected $request;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->imcService = $this->get('services.institution_medical_center');
        $this->institutionMedicalCenter = $this->imcService->findById($this->request->get('imcId', 0));
        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid medical center');
        }
    }
    
    /**
     * Add global award
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
    
                $html = $this->renderView('InstitutionBundle:MedicalCenter/Partials:row.globalAward.html.twig', array(
                    'award' => $award,
                    'institutionMedicalCenter' => $this->institutionMedicalCenter
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
     * Remove global award of an institution medical center
     * 
     * @param Request $request
     * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
     */
    public function ajaxRemoveGlobalAwardAction(Request $request)
    {
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id', 0));
        
        if (!$award) {
            throw $this->createNotFoundException();
        }
        
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        
        // get property value
        $property = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $award->getId());
        if ($property) {
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
        else {
            throw $this->createNotFoundException('Global award does not exist.');
        }
    }
}