<?php
namespace HealthCareAbroad\ApiBundle\Controller;
use HealthCareAbroad\ApiBundle\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
class InstitutionSpecializationApiController extends ApiController
{
    public function listAction(Request $request)
    {
        $knownFilters = array('status', 'specialization');
        $appliedFilters = $this->applyFiltersFromRequest($knownFilters);
        
        $result = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getResultByFilters($appliedFilters, Query::HYDRATE_ARRAY);
        
        $response = $this->createResponseAsJson(array('institutionSpecializations' => $result), 200);
        
        return $response;
    }
}