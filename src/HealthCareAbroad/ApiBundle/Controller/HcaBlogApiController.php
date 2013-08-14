<?php

namespace HealthCareAbroad\ApiBundle\Controller;

use Doctrine\DBAL\Driver\PDOConnection;

use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author Adelbert D. Silla
 *
 */
class HcaBlogApiController extends ApiController
{
    
    function getLatestBlogsAction(Request $request)
    {
        $conn = $this->getDoctrine()->getConnection('hca_blog');

        $results = $this->get('services.api.hcaBlog')->getBlogs($request->query->all());

        return $this->createResponseAsJson(array('posts' => $results), 200);
    }
}