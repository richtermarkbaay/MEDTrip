<?php
/**
 * Unit test for InstitutionUserService
 * 
 * @author Adelbert D. Silla
 */
namespace HealthCareAbroad\MedicalProcedureBundle\Tests\Services;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

use HealthCareAbroad\MedicalProcedureBundle\Tests\MedicalProcedureBundleTestCase;

use HealthCareAbroad\MedicalProcedureBundle\Services\MedicalProcedureService;

class MedicalProcedureServiceTest extends MedicalProcedureBundleTestCase
{
    protected $service;
    
    public function setUp()
	{
		$this->service = new MedicalProcedureService();
		$this->service->setContainer($this->getServiceContainer());
	}

	public function tearDown()
	{
		$this->service = null;
	}

	public function testCreateMedicalProcedureType()
	{
		try {
			
			for($i = 0; $i< 3; $i++) {
			
				$procedureType = new MedicalProcedureType();
				$procedureType->setDateCreated(new \DateTime())
				->setDateModified($procedureType->getDateCreated())
				->setName('ProcedureTypeTestUnit ' . $i)
				->setDescription('this is a test description')
				->setSlug('ProcedureTypeTestUnit')
				->setStatus(1);

				$procedureType = $this->service->saveMedicalProcedureType($procedureType);
			}
		} catch (\Exception $e) {
			$this->getDoctrine()->resetEntityManager();
			throw $e;
		}

		$this->assertNotEmpty($procedureType);
	}
	
	public function testSaveMedicalProcedure()
	{
		
		$procedureType = $this->getDoctrine()->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find(1);

		$procedure = new MedicalProcedure();
		$procedure->setMedicalProcedureType($procedureType);
		$procedure->setName('medProdTest FromUnitTest');
		$procedure->setSlug('medprodtest-fromunittest');
		$procedure->setStatus(1);
        
        try {
            $procedure = $this->service->saveMedicalProcedure($procedure);
        }
        catch (\Exception $e)
        {
            $this->getDoctrine()->resetEntityManager();
            throw $e;
        }
	    
	    
	    $this->assertNotEmpty($procedure);
	    
	    return $procedure;
	}
	
	public function testGetMedicalProcedure()
	{
		$procedure = $this->service->getMedicalProcedure(1);
		$this->assertNotEmpty($procedure);
	}

	public function testGetMedicalProcedureType()
	{
		$procedureType = $this->service->getMedicalProcedureType(1);
		$this->assertNotEmpty($procedureType);
	}

	public function testGetMedicalProcedureTypes()
	{
		$procedureTypes = $this->service->getMedicalProcedureTypes();
		var_dump(count($procedureTypes));
		$this->assertNotEmpty($procedureTypes);
	}
}