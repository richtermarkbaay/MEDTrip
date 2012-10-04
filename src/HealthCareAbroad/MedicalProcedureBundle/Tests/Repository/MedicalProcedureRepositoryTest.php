<?php 
namespace HealthCareAbroad\MedicalProcedureBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MedicalProcedureRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    public function setUp()
    {
		$kernel = static::createKernel();
		$kernel->boot();
		$this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testSearch()
    {
        $results = $this->_em->getRepository('MedicalProcedureBundle:TreatmentProcedure')->search('Test Treatment Procedure');

        $this->assertEquals(1, count($results));
    }
}