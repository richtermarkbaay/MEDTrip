<?php

namespace HealthCareAbroad\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\HelperBundle\Services\ErrorValidationHelper;
use Symfony\Component\Form\Form;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
abstract class ApiController extends Controller
{
    /**
     * Create a Response object with content type set to application/json
     * 
     * @param array $data
     * @param int $statusCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createResponseAsJson(array $data, $statusCode)
    {
        $response = $this->setResponseHeaders(
            new Response(\json_encode($data), $statusCode, array('content-type' => 'application/json'))
        );
        
        return $response;
    }
    
    
    /**
     * 
     * @param AbstractType $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createResponseFromFormErrors(Form $form)
    {
        $errors = array();
        ErrorValidationHelper::processFormErrorsDeeply($form, $errors);
        
        $response = $this->createResponseAsJson($errors, 400);
        
        return $response;
    }
    
    /**
     * 
     * @param Response $response
     * @return Response
     */
    protected function setResponseHeaders(Response $response)
    {
//         $seconds = 600;
//         $response->setPublic();
//         $response->setMaxAge($seconds);
//         $response->setSharedMaxAge($seconds);
//         $response->headers->addCacheControlDirective('must-revalidate', true);
//         $response->setETag(md5($response->getContent()));
//         $response->setVary(array('Accept-Encoding'));
//         $response->isNotModified($this->getRequest());
        
        return $response;
    }
    
    /**
     * Apply filters from request based on $knownFilters
     * 
     * @param array $knownFilters
     * @return array
     */
    protected function applyFiltersFromRequest(array $knownFilters)
    {
        $request = $this->getRequest();
        $appliedFilters = array();
        foreach ($knownFilters as $filterName) {
            $filterValue = $request->get($filterName, null);
            if (null !== $filterValue) {
                $appliedFilters[$filterName] = $filterValue;
            }
        }
        
        
        return $appliedFilters;
    }
}