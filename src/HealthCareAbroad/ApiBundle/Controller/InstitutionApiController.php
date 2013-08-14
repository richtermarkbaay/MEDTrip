<?php

namespace HealthCareAbroad\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class InstitutionApiController extends ApiController
{
    public function getBySlugAction(Request $request)
    {
        $institution = $this->get('services.api.institution')->findBySlug($request->get('slug'));
        
        return $this->createResponseAsJson(array('institution' => $institution), 200);
    }
}