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
        $this->addRouteUris = array(
            'country-center' => array(
                'uri' => '/test/sample-center',
                'variables' => json_encode(array('countryId' => 1, 'centerId' => 1))),
            'country-center-treatment' => array(
                'uri' => '/test/sample-center/procedure-type1',
                'variables' => json_encode(array('countryId' => 1, 'centerId' => 1, 'procedureTypeId' => 1))),
            'country-city-center' => array(
                'uri' => '/test/test/sample-center',
                'variables' => json_encode(array('countryId' => 1, 'cityId' => 1, 'centerId' => 1))),
            'country-city-center-treatment' => array(
                'uri' => '/test/test/sample-center/procedure-type1',
                'variables' => json_encode(array('countryId' => 1, 'cityId' => 1, 'centerId' => 1, 'procedureTypeId' => 1))),
            'country-city-center-clinic' => array(
                'uri' => '/test/test/sample-center/test-institution-medical-clinic',
                'variables' => json_encode(array('countryId' => 1, 'cityId' => 1, 'centerId' => 1, 'institutionId' => 1))),
            'country-city-center-clinic-treatment' => array(
                'uri' => '/test/test/sample-center/test-institution-medical-clinic/procedure-type1',
                'variables' => json_encode(array('countryId' => 1, 'cityId' => 1, 'centerId' => 1, 'institutionId' => 1, 'procedureTypeId' => 1)))
        );
    }

    public function testAddRouteCountryCenterReturnsNull()
    {
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-center'));
        $this->assertNull($this->service->addRoute('/non-existent-country/sample-center'));
        $this->assertNull($this->service->addRoute('/test/non-existent-center'));
    }

    public function testAddRouteCountryCenter()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-center'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    public function testAddRouteCountryCenterTreatmentReturnsNull()
    {
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/non-existent-country/sample-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/non-existent-country/sample-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/non-existent-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/test/sample-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/non-existent-center/procedure-type1'));
    }

    public function testAddRouteCountryCenterTreatment()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-center-treatment'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    public function testAddRouteCountryCityCenterReturnsNull()
    {
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/sample-center'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/non-existent-center'));
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/sample-center'));
        $this->assertNull($this->service->addRoute('/test/test/non-existent-center'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/sample-center'));
    }

    public function testAddRouteCountryCityCenter()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-city-center'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    public function testAddRouteCountryCityCenterTreatmentReturnsNull()
    {
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/sample-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/non-existent-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/sample-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/test/test/sample-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/test/non-existent-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/test/non-existent-center/procedure-type1'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/non-existent-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/sample-center/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/non-existent-center/procedure-type1'));
    }

    public function testAddRouteCountryCityCenterTreatment()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-city-center-treatment'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    public function testAddRouteCountryCityCenterClinicReturnsNull()
    {
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/sample-center/test-institution-medical-clinic'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/non-existent-center/test-institution-medical-clinic'));
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/sample-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center/test-institution-medical-clinic'));
        $this->assertNull($this->service->addRoute('/test/test/sample-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/test/test/non-existent-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/test/test/non-existent-center/test-institution-medical-clinic'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/non-existent-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/sample-center/non-existent-clinic'));
        $this->assertNull($this->service->addRoute('/test/non-existent-city/non-existent-center/test-institution-medical-clinic'));
    }

    public function testAddRouteCountryCityCenterClinic()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-city-center-clinic'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    public function testAddRouteCountryCityCenterClinicTreatmentReturnsNull()
    {
        // TODO: Add more assertions. This doesn't cover all possibilities.
        // On second thought some of the assertions are already covered in other test-for-null tests
        $this->assertNull($this->service->addRoute('/non-existent-country/non-existent-city/non-existent-center/non-existent-clinic/non-existent-treatment'));
        $this->assertNull($this->service->addRoute('/non-existent-country/test/sample-center/test-institution-medical-clinic/procedure-type1'));
        $this->assertNull($this->service->addRoute('/test/test/sample-center/test-institution-medical-clinic/non-existent-treatment'));
    }

    public function testAddRouteCountryCityCenterClinicTreatment()
    {
        self::$belayTeardownAfterClass = true;

        $addRouteUri = $this->addRouteUris['country-city-center-clinic-treatment'];
        $route = $this->service->addRoute($addRouteUri['uri']);

        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());
    }

    /**
     * @depends testAddRouteCountryCenter
     * @depends testAddRouteCountryCenterTreatment
     * @depends testAddRouteCountryCityCenter
     * @depends testAddRouteCountryCityCenterTreatment
     * @depends testAddRouteCountryCityCenterClinic
     * @depends testAddRouteCountryCityCenterClinicTreatment
     */
    public function testAddRoute()
    {
        $repository = $this->service->getDoctrine()->getRepository('FrontendBundle:FrontendRoute');

        $addRouteUri = $this->addRouteUris['country-center'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        $addRouteUri = $this->addRouteUris['country-center-treatment'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        $addRouteUri = $this->addRouteUris['country-city-center'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        $addRouteUri = $this->addRouteUris['country-city-center-treatment'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        $addRouteUri = $this->addRouteUris['country-city-center-clinic'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        $addRouteUri = $this->addRouteUris['country-city-center-clinic-treatment'];
        $route = $repository->findOneBy(array('uri' => $addRouteUri['uri']));
        $this->assertInstanceOf('HealthCareAbroad\FrontendBundle\Entity\FrontendRoute', $route);
        $this->assertEquals($addRouteUri['variables'], $route->getVariables());

        self::$belayTeardownAfterClass = false;
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