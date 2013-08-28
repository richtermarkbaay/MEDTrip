<?php

namespace HealthCareAbroad\AdminBundle\Controller;

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

class UnsecuredController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId'));
        
        if (!$this->institution) {
            throw $this->createNotFoundException("Invalid institution");
        }
        if ($this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($this->request->get('imcId', 0), true);
        }
    }
    
    /**
     * Load available global awards of an institution. Used in autocomplete fields
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGlobalAwardSourceAction(Request $request)
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

        $awards = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')
        ->getAvailableGlobalAwardsOfInstitution($this->institution, $options);


        foreach ($awards as $_award) {
            $output[] = array(
                'id' => $_award->getId(),
                'label' => $_award->getName(),
                'awardingBody' => $_award->getAwardingBody()->getName()
            );
        }
    
        return new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
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
    
    /**
     * Load available specializations for autocomplete field.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxSpecializationSourceAction(Request $request)
    {
        $start = \microtime(true);
        $output = array();
        $term = \trim($request->get('term', ''));
        
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')
            ->getAvailableSpecializationsByMedicalCenterId($this->institutionMedicalCenter->getId(), array('name' => $term));
        
        $end = \microtime(true); $diff = $end-$start;
        $output = array('specializations' => $specializations, 'executionTime' => $diff);
        $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        
        return $response; 
    }
}