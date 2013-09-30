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

/**
 * Service class for Treatment bundle. Accessible by services.treatment_bundle service key
 *
 *
 */
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

    private static $activeSpecializations;
    
    public function setMemcache(MemcacheService $memcache)
    {
        $this->memcache = $memcache;
    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $doctrine->getEntityManager();
    }

    /** ----- Start Specialization related functionalities ------ **/

    /**
     * Get Specializations with status ACTIVE
     *
     * @return array Specialization
     */
    public function getAllActiveSpecializations()
    {
        
        if(static::$activeSpecializations) {
            return static::$activeSpecializations;
        }
       
        // check in cache
        $key = 'TreatmentBundle:TreatmentBundleService:getAllActiveSpecializations';
        static::$activeSpecializations = $this->memcache->get($key);

        if (!static::$activeSpecializations) {
            static::$activeSpecializations = $this->doctrine->getRepository('TreatmentBundle:Specialization')
            ->getQueryBuilderForActiveSpecializations()
            ->getQuery()->getResult();

            // cache this result
            $this->memcache->set($key, static::$activeSpecializations);
        }
        return static::$activeSpecializations;
    }

    /**
     * Get an Specialization by Id. Apply caching here
     *
     * @param int $id
     * @return Specialization
     */
    public function getSpecialization($id)
    {
        return $this->doctrine->getRepository('TreatmentBundle:Specialization')->find($id);
    }

    public function getSpecializationBySlug($slug)
    {
        static $specializationsBySlug = array();
        if (!\array_key_exists($slug, $specializationsBySlug)){
            $specializationsBySlug[$slug] = $this->doctrine->getRepository('TreatmentBundle:Specialization')->findOneBySlug($slug);
        }

        return $specializationsBySlug[$slug];
    }

    /** ----- Endi Specialization related functionalities ------ **/


    /** ----- Start Sub Specialization related functionalities ------ **/

    /**
     * Get all active SubSpecializations of Specialization
     *
     * @param Specialization $specialization
     */

    public function getActiveSubSpecializationsBySpecialization(Specialization $specialization)
    {
        $key = 'TreatmentBundle:TreatmentService:getActiveSubSpecializationBySpecialization_'.$specialization->getId();
        $result = $this->memcache->get($key);
        if (true) {
            $result = $this->doctrine->getRepository('TreatmentBundle:SubSpecialization')
            ->getQueryBuilderForGettingAvailableSubSpecializations($specialization)
            ->getQuery()
            ->getResult();

            //$this->memcache->set($key, $result);
        }

        return $result;
    }

    /**
     * Get a SubSpecialization by id. Apply caching here
     *
     * @param int $id
     */
    public function getSubSpecialization($id)
    {
        return $this->entityManager->getRepository('TreatmentBundle:SubSpecialization')->find($id);
    }

    public function getSubSpecializationBySlug($slug)
    {
        static $subSpecializationsBySlug = array();
        if (!\array_key_exists($slug, $subSpecializationsBySlug)){
            $subSpecializationsBySlug[$slug] = $this->doctrine->getRepository('TreatmentBundle:SubSpecialization')->findOneBySlug($slug);
        }

        return $subSpecializationsBySlug[$slug];
    }

    public function saveSubSpecialization(SubSpecialization $entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        return $entity;
    }

    /** ----- End Sub Specialization related functionalities ------ **/

    /** ----- Start Treatment related functionalities ------ **/

    public function getTreatment($id)
    {
        return $this->entityManager->getRepository('TreatmentBundle:Treatment')->find($id);
    }

    public function getTreatmentBySlug($slug)
    {
        static $treatmentsBySlug = array();
        if (!\array_key_exists($slug, $treatmentsBySlug)) {
            $treatmentsBySlug[$slug] = $this->doctrine->getRepository('TreatmentBundle:Treatment')->findOneBySlug($slug);
        }

        return $treatmentsBySlug[$slug];
    }

    public function findTreatmentsByIds(array $ids)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $query = $qb->select('a')
            ->from('TreatmentBundle:Treatment', 'a')
            ->where($qb->expr()->in('a.id', ':ids'))
            ->setParameter('ids', $ids)->getQuery();

        return $query->getResult();
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

    public function getTreatmentsBySpecializationIdGroupedBySubSpecialization($specializationId)
    {
        $result = $this->doctrine->getRepository('TreatmentBundle:Treatment')
                ->getBySpecializationId($specializationId, true);

        $treatments = array();

        //Holder for treatments with no subspecialization. We want this
        //at the end of the treatments array;
        $treatmentsWithNoSubSpecialization = array();

        foreach($result as $each)
        {
            if(!$each['subSpecializationId']) {
                $treatmentsWithNoSubSpecialization[] = $each;
            } else {
                $treatments[$each['subSpecializationName']][] = $each;
            }
        }

        //TODO: The group label should be modified on the view layer but it's
        // cleaner or simpler this way
        if ($treatmentsWithNoSubSpecialization) {
            $treatmentsWithNoSubSpecializationLabel = empty($treatments) ? 'Treatments' : 'Other Treatments';
            $treatments[$treatmentsWithNoSubSpecializationLabel] = $treatmentsWithNoSubSpecialization;
        }

        return $treatments;
    }

    public function getTreatmentsBySpecializationGroupedBySubSpecialization(Specialization $specialization)
    {
        return $this->getTreatmentsBySpecializationIdGroupedBySubSpecialization($specialization->getId());
    }

    /** ----- End Treatment related functionalities ------ **/
}