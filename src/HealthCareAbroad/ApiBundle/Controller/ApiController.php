<?php

namespace HealthCareAbroad\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
     * @return Response
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
     * @param Response $response
     * @return Response
     */
    protected function setResponseHeaders(Response $response)
    {
        $seconds = 600;
        $response->setPublic();
        $response->setMaxAge($seconds);
        $response->setSharedMaxAge($seconds);
        $response->setVary(array('Accept-Encoding'));
        
        return $response;
    }
}