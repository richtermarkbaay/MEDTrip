<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;
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

	public function getMedicalProcedure($id)
	{
		$result = $this->entityManager->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($id);
		return $result;
	}
	
	public function getMedicalProcedureType($id)
	{
		$result = $this->entityManager->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($id);
		return $result;
		
	}

	public function saveMedicalProcedure(MedicalProcedure $entity)
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);

		return $entity;
	}

	public function saveMedicalProcedureType(MedicalProcedureType $entity)
	{
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
	
		return $entity;
	}
	
	public function deleteMedicalProcedure($id)
	{
		$entity = $this->entityManager->getRepository('MedicalProcedureBundle:MedicalProcedure')->find($id);
		$entity->setStatus(0);
		$this->entityManager->persist($entity);
		$this->entityManager->flush($entity);
	}
}