<?php
/*
 * author Alnie L. Jacobe
 */
namespace HealthCareAbroad\SearchBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilderFactory;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService;

use HealthCareAbroad\SearchBundle\Services\AdminSearchService;

use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\MedicalProcedureBundle\Entity\ProcedureType;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Procedure;

class AdminSearchServiceTest extends ContainerAwareUnitTestCase
{
    /**
     *
     * @var HealthCareAbroad\SearchBundle\Services\AdminSearchService
     */
    protected $service;
    
    /**
     *
     * @var HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService
     */
    protected $pagerService;
    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected static $router;
    
    /**
     * @var SearchResultBuilderFactory
     */
    private $factory;
    public function setUp()
    {
        $this->service = new AdminSearchService();
        $factory = new SearchResultBuilderFactory($this->getServiceContainer()->get('doctrine'));
        $factory->setRouter($this->getRouter());     
        $this->service->setSearchBuilderFactory($factory);
        
    }
    
    public function tearDown()
    {
        $this->service = null;
    }
    public function testsearch()
    {
        $p = new SearchAdminPagerService();
        $params = array('term' => 'a', 'category' => '1', 'page' => 1);
        $adminSearchResults =  $this->service->search($params, $p);
        $this->assertNotEmpty($adminSearchResults);
        
        return $adminSearchResults;
    }
    
    public function testGetPager()
    {
        $this->pagerService = new SearchAdminPagerService();
        $pager = $this->pagerService->getPager();
        
        return $pager;
    }
    
}