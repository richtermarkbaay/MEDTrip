<?php
namespace HealthCareAbroad\ApiBundle\Controller;
use HealthCareAbroad\ApiBundle\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
class TreatmentApiController extends ApiController
{
    public function listAction(Request $request)
    {
        $knownFilters = array('status', 'specialization', 'subSpecialization');
        $appliedFilters = $this->applyFiltersFromRequest($knownFilters);
                
        $result = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')
            ->getResultByFilters($appliedFilters, Query::HYDRATE_ARRAY);
        
        $response = $this->createResponseAsJson(array('treatments' => $result), 200);
        
        return $response;
    }
}