<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\MedicalProcedureundle\Repository\MedicalProcedureRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class MedicalProcedureService
{
	private $container;
	private $entityManager;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
		$this->entityManager = $this->container->get('doctrine')->getEntityManager();
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

	public function saveMedicalProcedure(MedicalProcedure $entity)
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
	
	public function deleteMedicalProcedure($id)
	{
		$entity = $this->entityManager->getRepository('MedicalProcedureBundle:TreatmentProcedure')->find($id);
		$entity->setStatus(0);
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
	}
}