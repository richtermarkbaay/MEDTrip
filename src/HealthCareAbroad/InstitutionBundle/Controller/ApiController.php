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
}