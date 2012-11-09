<?php 
namespace HealthCareAbroad\TreatmentBundle\Tests\Repository;

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
        $results = $this->_em->getRepository('TreatmentBundle:TreatmentProcedure')->search('Test Treatment Procedure');

        $this->assertEquals(1, count($results));
    }
}