<?php
namespace HealthCareAbroad\TreatmentBundle\Services;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;
use HealthCareAbroad\TreatmentBundle\Repository\TreatmentRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

use Symfony\Component\DependencyInjection\ContainerInterface;


class TreatmentBundleService
{
    /**
     * @var MemcacheService
     */
    private $memcache;

    /**
     * @var Registry
     */
    private $doctrine;

    public function setMemcache(MemcacheService $memcache)
    {
        $this->memcache = $memcache;
    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get Specializations with status ACTIVE
     *
     * @return array Specialization
     */
    public function getAllActiveSpecializations()
    {
        // check in cache
        $key = 'TreatmentBundle:TreatmentBundleService:getAllActiveSpecializations';
        $result = $this->memcache->get($key);
        if (!$result) {
            $result = $this->doctrine->getRepository('TreatmentBundle:Specialization')
            ->getQueryBuilderForActiveSpecializations()
            ->getQuery()->getResult();

            // cache this result
            $this->memcache->set($key, $result);
        }

        return $result;
    }

    public function getSubSpecialization($id)
    {
        return $this->entityManager->getRepository('TreatmentBundle:SubSpecialization')->find($id);
    }

    public function saveSubSpecialization(SubSpecialization $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        return $entity;
    }

    public function getTreatment($id)
    {
        return $this->entityManager->getRepository('TreatmentBundle:Treatment')->find($id);
    }

    public function saveTreatment(Treatment $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        return $entity;
    }

    public function deleteTreatment($id)
    {
        $entity = $this->entityManager->getRepository('TreatmentBundle:Treatment')->find($id);
        $entity->setStatus(0);
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);
    }

    public function getActiveTreatmentsBySpecialization(Specialization $specialization)
    {
        $key = 'TreatmentBundle:TreatmentService:getActiveTreatmentsBySpecialization_'.$specialization->getId();
        $result = $this->memcache->get($key);
        if (!$result) {
            $result = $this->doctrine->getRepository('TreatmentBundle:Treatment')
            ->getQueryBuilderForActiveTreatmentsBySpecialization($specialization)
            ->getQuery()
            ->getResult();

            // store to memcache
            $this->memcache->set($key, $result);
        }

        return $result;
    }
}