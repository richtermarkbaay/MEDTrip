<?php
namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ResponseHeadersController extends Controller
{
    protected function setResponseHeaders(Response $response, array $options = array())
    {
//         $response->setPublic();
//         $response->setMaxAge(600);
//         $response->setSharedMaxAge(600);
//         //$response->setVary(array('Accept-Encoding', 'User-Agent'));
//         $response->setVary(array('Accept-Encoding'));
//         $response->headers->addCacheControlDirective('must-revalidate', true);
//         $response->setETag(md5($response->getContent()));
//         //$response->setLastModified($date);
//         $response->isNotModified($this->getRequest());

        return $response;
    }

    protected function setStaticPageResponseHeaders(Response $response, array $options = array())
    {
        //1 week
        //$seconds = 604800;

        // set to 1 week or even longer once we know that the content for our
        // static pages (about-us, terms-of-use, privacy-policy) have been finalized
        $seconds = 600;

        $response->setPublic();
        $response->setMaxAge($seconds);
        $response->setSharedMaxAge($seconds);
        $response->setVary(array('Accept-Encoding'));

        return $response;
    }
}