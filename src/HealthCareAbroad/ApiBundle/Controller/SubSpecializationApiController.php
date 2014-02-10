<?php
namespace HealthCareAbroad\ApiBundle\Controller;
use HealthCareAbroad\ApiBundle\Controller\ApiController;
use Doctrine\ORM\Query;
class SubSpecializationApiController extends  ApiController
{
    public function listAction()
    {
        $knownFilters = array('status', 'specialization');
        $appliedFilters = $this->applyFiltersFromRequest($knownFilters);
        
        $result = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')
        ->getResultByFilters($appliedFilters, Query::HYDRATE_ARRAY);
        
        $response = $this->createResponseAsJson(array('subSpecializations' => $result), 200);
        
        return $response;
    }
}