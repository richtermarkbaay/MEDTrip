<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Services;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\MedicalProcedureundle\Repository\TreatmentProcedureRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class TreatmentProcedureService
{
	private $entityManager;
	
	/**
	 * @var Registry
	 */
	private $doctrine;
	
	/**
	 * @var MemcacheService
	 */
	private $memcache;
	
	public function setDoctrine(Registry $doctrine)
	{
	    $this->doctrine = $doctrine;
	    $this->entityManager = $this->doctrine->getEntityManager();
	}
	
	public function setMemcache(MemcacheService $memcache)
	{
	    $this->memcache = $memcache;
	}

	public function getTreatmentProcedure($id)
	{
		$result = $this->entityManager->getRepository('MedicalProcedureBundle:TreatmentProcedure')->find($id);
		return $result;
	}
	
	public function getTreatment($id)
	{
		$result = $this->entityManager->getRepository('MedicalProcedureBundle:Treatment')->find($id);
		return $result;
		
	}

	public function saveTreatmentProcedure(TreatmentProcedure $entity)
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);

		return $entity;
	}

	public function saveTreatment(Treatment $entity)
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
	
		return $entity;
	}
	
	public function deleteTreatmentProcedure($id)
	{
		$entity = $this->entityManager->getRepository('MedicalProcedureBundle:TreatmentProcedure')->find($id);
		$entity->setStatus(0);
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
	}
	
	public function getActiveTreatmentProceduresByMedicalCenter(MedicalCenter $medicalCenter)
	{
	    $key = 'MedicalProcedureBundle:TreatmentProcedureService:getActiveTreatmentProceduresByMedicalCenter_'.$medicalCenter->getId();
	    $result = $this->memcache->get($key);
	    if (!$result) {
	        $result = $this->doctrine->getRepository('MedicalProcedureBundle:TreatmentProcedure')
	            ->getQueryBuilderForActiveTreatmentProceduresByMedicalCenter($medicalCenter)
	            ->getQuery()
	            ->getResult();
	        
	        // store to memcache
	        $this->memcache->set($key, $result);
	    }
	    
	    return $result;
	}
}