<?php
namespace HealthCareAbroad\FrontendBundle\Tests\Services;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\BrowserKit\Cookie;

use HealthCareAbroad\FrontendBundle\Entity\FrontendRoute;

use HealthCareAbroad\FrontendBundle\Services\FrontendRouteService;

use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleTestCase;

class FrontendRouteServiceTest extends FrontendBundleTestCase
{
    /**
     * @var FrontendRouteService
     */
    private $service;
    
    /**
     * @var array
     */
    private $commonVariables;
    
    /**
     * @var Request
     */
    private $request;

    public function setUp()
    {
        $this->service = new FrontendRouteService();
        $this->service->setLogger($this->getServiceContainer()->get('logger'));
        $this->service->setDoctrine($this->getServiceContainer()->get('doctrine'));
        $this->service->setSession($this->getServiceContainer()->get('session'));
        $this->request = Request::createFromGlobals();
        $this->service->setRequest($this->request);
        $this->commonVariables = array('countryId' => 1, 'cityId' => '1');
    }
    
    public function testMatchFromSession()
    {
        $uri = '/usa/new-york/for-session-storage';
        $jsonVars = \json_encode($this->commonVariables);
        $session = $this->getServiceContainer()->get('session');
        $session->set(\md5($uri), $jsonVars);
        
        $route = $this->service->match($uri);
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($jsonVars, $route->getVariables());
    }
    
    public function testMatchFromCookie()
    {
        $uri = '/usa/new-york/for-cookie-storage';
        $jsonVars = \json_encode($this->commonVariables);
        $this->request->cookies->set(\md5($uri), $jsonVars);
        
        $route = $this->service->match($uri);
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($jsonVars, $route->getVariables());
        
    }
    
    public function testMatchFromDatabase()
    {
        $uri = '/usa/new-york/some-data/for-database-storage';
        $jsonVars = \json_encode($this->commonVariables);
        
        $route = $this->service->match($uri);
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($jsonVars, $route->getVariables());
    }
}