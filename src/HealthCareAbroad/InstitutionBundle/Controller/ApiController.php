<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    /**
     * Retrieve list of institutions
     * Url: /api/institutions
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function institutionsAction(Request $request)
    {
        // load approved institutions by default
        $institutions = $this->get('services.institution.factory')->findAllApproved();
        $data = array();
        foreach ($institutions as $_each) {
            $data[] = array(
                'id' => $_each->getId(),
                'name' => $_each->getName(),
                'label' => $_each->getName()
            );
        }
        
        $response = new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
        
        return $response;
    }
    
    /**
     * Retrieve list of active medical centers
     * Url: /api/institution_medical_centers
     * Known parameters:
     *     institutionId
     * @param Request $request
     */
    public function listInstitutionMedicalCentersAction(Request $request)
    {
        if ($request->get('institutionId')) {
            $institution = $this->get('services.institution.factory')->findById($request->get('institutionId'));
            if (!$institution) {
                throw $this->createNotFoundException('Invalid institution');
            }
        }
        
        
        $clinics = $this->get('services.institution')->getActiveMedicalCenters($institution);
        $data = array();
        foreach ($clinics as $_each){
            $data[] = array(
                'id' => $_each->getId(),
                'name' => $_each->getName(),
                'label' => $_each->getName(),
                'institutionId' => $institution->getId()
            );
        }
        $response = new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
        
        return $response;
    }
    
    /**
     * Retrieve list of active medical centers
     * Url: /api/medical-centers
     * Known parameters:
     *     institutionId
     * @param Request $request
     */
    public function listMedicalCentersAction(Request $request)
    {
    
        $clinics = $this->get('services.institution_medical_center')->getApprovedMedicalCenters();
        $data = array();
        foreach ($clinics as $_each){
            $data[] = array(
                            'id' => $_each->getId(),
                            'name' => $_each->getName(),
                            'label' => $_each->getName(),
            );
        }
        $response = new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
    
        return $response;
    }
}