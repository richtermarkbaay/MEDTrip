<?php
/**
 * Frontend Route service
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\FrontendBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Bridge\Monolog\Logger;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\FrontendBundle\Entity\FrontendRoute;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FrontendRouteService
{
    
    /**
     * @var array
     */
    private static $storage = array();
    
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var Session
     */
    private $session;
    
    /**
     * @var Request
     */
    private $request;
    
    private $logContext=array('Frontend Route');
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function setSession(Session $session)
    {
        $this->session = $session;
    }
    
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    
    /**
     * Match a URI to listed frontend dynamic routes
     * First step is to check if session variable $dynamicRoute is set, which means this has been directed from search form.
     * If not set in session, we next check the cookie for previously set urls.
     * If not set in cookie, we now refer to the database
     * 
     * @param string $uri
     * @return FrontendRoute
     */
    public function match($uri)
    {
        // check first if this uri has already been stored in static storage
        if (\array_key_exists($uri, static::$storage)) {
            $route = static::$storage[$uri];
        }
        else {
            
            if ($route = $this->matchFromSession($uri)) {
                // store this in storage
                static::$storage[$uri] = $route;
            }
        }
        
        return $route;
    }
    
    /**
     * Match a frontend route by session key
     * 
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromSession($uri)
    {
        $routeObj = null;
        if ($vars = $this->session->get(\md5($uri), null)) {
            $routeObj = new FrontendRoute();
            $routeObj->setVariables($vars);
            $this->logger->info("Matched uri '{$uri}' from session with variables: {$vars}");
        }
        
        return $routeObj ? $routeObj : $this->matchFromCookie($uri);
    }
    
    /**
     * Find a matching frontend route in the cookie by uri
     * 
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromCookie($uri)
    {
        $routeObj = null;
        if ($vars = $this->request->cookies->get(\md5($uri),null)) {
            $routeObj = new FrontendRoute();
            $routeObj->setVariables($vars);
            $this->logger->info("Matched uri '{$uri}' from cookie with variables: {$vars}");
        }
        
        return $routeObj ? $routeObj : $this->matchFromDatabase($uri);
    }
    
    /**
     * Find a record in frontend_routes by uri
     * 
     * @param string $uri
     * @return FrontendRoute
     */
    private function matchFromDatabase($uri)
    {
        $routeObj = $this->doctrine->getRepository('FrontendBundle:FrontendRoute')->findOneBy(array('uri' => $uri));
        
        if ($routeObj) {
            $this->logger->info("Matched uri '{$uri}' from database with variables: {$routeObj->getVariables()}");
        }
        
        return $routeObj;
    }
}