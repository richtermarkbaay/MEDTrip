<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

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
        parent::preExecute();
        $this->request = $this->getRequest();
        $this->imcService = $this->get('services.institution_medical_center');
        $this->institutionMedicalCenter = $this->imcService->findById($this->request->get('imcId', 0));
        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid medical center');
        }
    }
    
//     /** This is an old code for addding globl award
//      * Add global award
//      * 
//      * @param Request $request
//      * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
//      */
//     public function ajaxAddGlobalAwardAction(Request $request)
//     {
//        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id'));
    
//         if (!$award) {
//             throw $this->createNotFoundException();
//         }
    
//         $propertyService = $this->get('services.institution_medical_center_property');
//         $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
//         // check if this medical center already have this property
//         if ($this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $award->getId())) {
//             $response = new Response("Award {$award->getId()} already exists.", 500);
//         }
//         else {
//             $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
//             $property->setValue($award->getId());
//             try {
//                 $em = $this->getDoctrine()->getEntityManager();
//                 $em->persist($property);
//                 $em->flush();
    
//                 $html = $this->renderView('InstitutionBundle:MedicalCenter/Partials:row.globalAward.html.twig', array(
//                     'award' => $award,
//                     'property' => $property,
//                     'institution' => $this->institution,
//                     'institutionMedicalCenter' => $this->institutionMedicalCenter,
//                     'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
//                 ));
                
//                 $calloutView = $this->_getEditMedicalCenterCalloutView();
    
//                 $response = new Response(\json_encode(array('html' => $html, 'calloutView' => $calloutView)), 200, array('content-type' => 'application/json'));
//             }
//             catch (\Exception $e){
//                 $response = new Response($e->getMessage(), 500);
//             }
//         }
    
//         return $response;
//     }
    
    /**
     * Remove global award of an institution medical center
     *
     * @param Request $request
     * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
     */
    public function ajaxRemoveGlobalAwardAction(Request $request)
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

                $responseContent = array('id' => $request->get('id'), 'calloutView' => $this->_getEditMedicalCenterCalloutView());
                $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
    
        return $response;
    }
    
    public function ajaxEditGlobalAwardAction()
    {
        $globalAward = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($this->request->get('globalAwardId', 0));
        if (!$globalAward) {
            throw $this->createNotFoundException('Invalid global award');
        }
        $propertyType = $this->get('services.institution_property')->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $imcProperty = $this->imcService->getPropertyValue($this->institutionMedicalCenter, $propertyType, $globalAward->getId() , $this->request->get('propertyId', 0));
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
                    $extraValue = \json_decode($imcProperty->getExtraValue(), true);
                    $yearAcquired = \implode(', ',$extraValue[InstitutionGlobalAwardExtraValueDataTransformer::YEAR_ACQUIRED_JSON_KEY]);
                    $output = array(
                        'targetRow' => '#globalAwardRow_'.$imcProperty->getId(),
                        'html' => $yearAcquired,
//                         'calloutView' => $this->_getEditMedicalCenterCalloutView()
                    );
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Form error', 400);
            }
        }
        
        return $response;
    }
    
    /**
     * Load available global awards of an institution. Used in autocomplete fields
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxMedicalCenterGlobalAwardSourceAction(Request $request)
    {
        $term = \trim($request->get('term', ''));
        $type = $request->get('type', null);
        $types = \array_flip(GlobalAwardTypes::getTypeKeys());
        $type = \array_key_exists($type, $types) ? $types[$type] : 0;
    
        $output = array();
        $options = new QueryOptionBag();
        $options->add('globalAward.name', $term);
        if ($type) {
            $options->add('globalAward.type', $type);
        }
    
        $awards = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')
        ->getAvailableGlobalAwardsOfInstitutionMedicalCenter($this->institutionMedicalCenter, $options);
    
    
        foreach ($awards as $_award) {
            $output[] = array(
                'id' => $_award->getId(),
                'label' => $_award->getName(),
                'awardingBody' => $_award->getAwardingBody()->getName()
            );
        }
    
        return new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }

    private function _getEditMedicalCenterCalloutView()
    {
        $calloutParams = array(
            '{CENTER_NAME}' => $this->institutionMedicalCenter->getName(),
            '{ADD_CLINIC_URL}' => $this->generateUrl('institution_medicalCenter_add')
        );
        $calloutMessage = $this->get('services.institution.callouts')->get('success_edit_center', $calloutParams);
        $calloutView = $this->renderView('InstitutionBundle:Widgets:callout.html.twig', array('callout' => $calloutMessage));
    
        return $calloutView;
    }
}