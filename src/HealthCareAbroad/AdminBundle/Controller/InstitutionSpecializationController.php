<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionSpecializationController extends Controller
{
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    /**
     * @var InstitutionSpecialization
     */
    private $institutionSpecialization;
    
    /**
     * @var Request
     */
    private $request;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }
    
        // check InstitutionMedicalCenter
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }
        }
        
        // check  Institution specialization
        if ($isId = $this->request->get('isId', 0)) {
            $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
                ->find($isId);
            if (!$this->institutionSpecialization) {
                throw $this->createNotFoundException("Invalid insitution specialization {$isId}");
            }
        }
        
    }
    
    /**
     * Remove a Treatmnent from an institution specialization
     * Expected parameters
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRemoveSpecializationTreatmentAction()
    {
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($this->request->get('tId', 0));
    
        if (!$this->institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization {$this->institutionSpecialization->getId()}.");
        }
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$treatment->getId()}.");
        }
        $this->institutionSpecialization->removeTreatment($treatment);

        try {
    
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionSpecialization);
            $em->flush();
            $response = new Response("Treatment removed", 200);
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
        }
    
        return $response;
    }
}