<?php 
namespace HealthCareAbroad\SearchBundle\Tests;

//require_once __DIR__.'/../../../../app/AppKernel.php';

/**
 * Keep in mind that each single test method in your test case won't be isolated 
 * from each other because the container is shared for the whole test case. 
 * Modifications to the container may have impact on you other test methods, 
 * however, the test cases will run faster because you will not need to 
 * instantiate and boot a new kernel for each test methods.
 * 
 * This should probably not be called ContainerAwareUnitTestCase since we have
 * a handle to the container and the test case can easily degenerate into a 
 * functional test.
 * 
 * TODO: assess if we can refrain from using this class
 * 
 * @author harold
 *
 */
class ContainerAwareUnitTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    protected static $container;

    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }

    public function get($serviceId)
    {
        return self::$kernel->getContainer()->get($serviceId);
    }
}