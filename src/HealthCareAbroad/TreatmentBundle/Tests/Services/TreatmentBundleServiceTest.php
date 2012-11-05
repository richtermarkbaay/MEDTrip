<?php
/**
 * Unit test for InstitutionUserService
 *
 * @author Adelbert D. Silla
 */
namespace HealthCareAbroad\TreatmentBundle\Tests\Services;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;
use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;
use HealthCareAbroad\TreatmentBundle\Services\TreatmentBundleService;
use HealthCareAbroad\TreatmentBundle\Tests\TreatmentBundleTestCase;

class TreatmentBundleServiceTest extends TreatmentBundleTestCase
{
    protected $service;

    public function setUp()
    {
        $this->service = new TreatmentBundleService();
        $this->service->setContainer($this->getServiceContainer());
    }

    public function tearDown()
    {
        $this->service = null;
    }

    public function testCreateTreatment()
    {
        $medicalCenter = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find(1);

        try {

            for($i = 0; $i< 3; $i++) {

                $procedureType = new Treatment();
                $procedureType->setDateCreated(new \DateTime())
                ->setDateModified($procedureType->getDateCreated())
                ->setName('ProcedureTypeTestUnit ' . $i)
                ->setDescription('this is a test description')
                ->setSlug('ProcedureTypeTestUnit')
                ->setStatus(1)
                ->setSpecialization($medicalCenter);

                $procedureType = $this->service->saveTreatment($procedureType);
            }
        } catch (\Exception $e) {
            $this->getDoctrine()->resetEntityManager();
            throw $e;
        }

        $this->assertNotEmpty($procedureType);
    }

    public function testSaveTreatmentProcedure()
    {

        $procedureType = $this->getDoctrine()->getEntityManager()->getRepository('TreatmentBundle:Treatment')->find(1);

        $procedure = new TreatmentProcedure();
        $procedure->setTreatment($procedureType);
        $procedure->setName('medProdTest FromUnitTest');
        $procedure->setSlug('medprodtest-fromunittest');
        $procedure->setStatus(1);

        try {
            $procedure = $this->service->saveTreatmentProcedure($procedure);
        }
        catch (\Exception $e)
        {
            $this->getDoctrine()->resetEntityManager();
            throw $e;
        }


        $this->assertNotEmpty($procedure);

        return $procedure;
    }

    public function testGetTreatmentProcedure()
    {
        $procedure = $this->service->getTreatmentProcedure(1);
        $this->assertNotEmpty($procedure);
    }

    public function testGetTreatment()
    {
        $procedureType = $this->service->getTreatment(1);
        $this->assertNotEmpty($procedureType);
    }
}