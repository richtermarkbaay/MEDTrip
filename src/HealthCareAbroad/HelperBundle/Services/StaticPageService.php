<?php

namespace HealthCareAbroad\HelperBundle\Services;
/*
 * author Alnie Jacobe
 */
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bridge\Monolog\Logger;

use HealthCareAbroad\AdminBundle\Entity\StaticPage;

use HealthCareAbroad\AdminBundle\Repository\StaticPageRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

class StaticPageService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var StaticPageRepository
     */
    private $repository;
    
    /**
     * @var array
     */
    private static $storage = array();
    
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * @var Session
     */
    private $session;
    
    /**
     * @var Request
     */
    private $request;
    
    /**
     *
     * @var unknown
     */
    private $routeResolver;
    
    private $logContext=array('Frontend Route');
    
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function getDoctrine()
    {
        return $this->doctrine;
    }
    
    public function setSession(Session $session)
    {
        $this->session = $session;
    }
    
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('AdminBundle:StaticPage');
    }
    
    public function getUrlforStaticPagebyTitleAndSection($title, $section)
    {
       $sluggableTitle = str_replace(" ", "-", $title);
       if($section == StaticPage::SECTION_ADMIN) {
           $url = '/admin/'. $sluggableTitle . '.html';
       }
       else if ($section == StaticPage::SECTION_CLIENT_ADMIN) {
           $url = '/institution/'. $sluggableTitle . '.html';
       }
       else {
           $url = '/'. $sluggableTitle . '.html';
       }
       return $url;
    }
    /**
     * Match a URI to listed frontend dynamic routes
     * First step is to check if session variable $dynamicRoute is set, which means this has been directed from search form.
     * If not set in session, we next check the cookie for previously set urls.
     * If not set in cookie, we now refer to the database
     *
     * @param string $uri
     * @return StaticPageRoute
     */
    public function match($uri)
    {
        // check first if this uri has already been stored in static storage
        if (array_key_exists($uri, static::$storage)) {
            return static::$storage[$uri];
        }
    
        $route = null;
        //         if (is_null($route = $this->matchFromSession($uri))) {
        //             if (is_null($route = $this->matchFromCookie($uri))) {
        $route = $this->matchFromDatabase($uri);
        //             }
        //         }
    
        if ($route) {
            // save in local storage
            static::$storage[$uri] = $route;
        }
    
        return $route;
    }
    
    /**
     * Find a record in static_page_routes by uri
     *
     * @param string $uri
     * @return StaticPageRoute
     */
    private function matchFromDatabase($uri)
    {
        $routeObj = $this->doctrine->getRepository('AdminBundle:StaticPage')->findOneBy(array('url' => $uri));
    
        if ($routeObj) {
            $this->logger->info("Matched uri '{$uri}' from database");
        }
    
        return $routeObj;
    }
}