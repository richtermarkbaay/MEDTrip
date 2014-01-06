<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

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
                $imcProperty = $editGlobalAwardForm->getData(); 
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($imcProperty);
                $em->flush();

                // Invalidate InstitutionMedicalCenterProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                $output = array(
                    'status' => true,
                    'extraValue' => $imcProperty->getExtraValue(),
                );

                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response('Form error', 400);
            }
        }
        
        return $response;
    }
}