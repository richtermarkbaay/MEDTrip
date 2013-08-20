<?php
namespace HealthCareAbroad\TreatmentBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\LockMode;
/**
 * SpecializationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SpecializationRepository extends EntityRepository
{
    /**
     * Overrides find() accepting either id or slug.
     *
     * (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::find()
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
        if (is_numeric($id)) {
            return parent::find($id, $lockMode, $lockVersion);
        }

        return $this->findBy(array('slug' => $id));
    }

    public function search($term = '', $limit = 10)
    {
        $dql = "
            SELECT c
            FROM TreatmentBundle:Specialization AS c
            WHERE c.name LIKE :term
            ORDER BY c.name ASC";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('term', "%$term%");
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function autoCompleteSearch($term = '', $limit = 10)
    {
        $dql = "SELECT Specialization.id, Specialization.name as value
                FROM TreatmentBundle:Specialization AS Specialization
                WHERE Specialization.name LIKE :term
                AND Specialization.status = :status
                ORDER BY Specialization.name ASC";

        $query = $this->_em->createQuery($dql);
        $query->setMaxResults($limit);
        $query->setParameter('status', Specialization::STATUS_ACTIVE);
        $query->setParameter('term', "%$term%");

        return $query->getArrayResult();
    }

    public function getSpecializationSearchByName($term, $ids)
    {
        $dql = "SELECT Specialization.id, Specialization.name as value
        FROM TreatmentBundle:Specialization AS Specialization
        WHERE Specialization.name LIKE :term
        AND Specialization.status = :status
        ORDER BY Specialization.name ASC";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('status', Specialization::STATUS_ACTIVE);
        $query->setParameter('term', "%$term%");

        return $query->getArrayResult();

    }

    /**
     * Get Active Specializations
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForActiveSpecializations()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')
            ->from('TreatmentBundle:Specialization', 'a')
            ->add('where', 'a.status = :status')
            ->orderBy('a.name')
            ->setParameter('status', Specialization::STATUS_ACTIVE);

        return $qb;
    }

    private function getIdsOfSpecializationsWithNoSubSpecializations()
    {
        $dql = "
            SELECT a.id
            FROM TreatmentBundle:Specialization AS a
            LEFT JOIN a.subSpecializations AS b
            WHERE a.status=1 AND b.id IS NULL";

        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    /**
     * Get Active Specializations
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getActiveSpecializations()
    {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a')
        ->from('TreatmentBundle:Specialization', 'a')
        ->add('where', 'a.status = :status')
        ->orderBy('a.name')
        ->setParameter('status', Specialization::STATUS_ACTIVE);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get Active Specializations that still not assign
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getAvailableSpecializations($assignedSpecializations)
    {
        $ids = array();
        foreach ($assignedSpecializations as $each) {
            $ids[] = $each->getSpecialization()->getId();
        }

        $idsNotIn = "'".\implode("', '",$ids)."'";

        $dql = "SELECT a,t FROM TreatmentBundle:Specialization a INNER JOIN a.treatments t WHERE a.status = :active AND a.id NOT IN ({$idsNotIn}) ORDER BY a.name ASC";
        $query = $this->getEntityManager()->createQuery($dql)
        ->setParameter('active', Specialization::STATUS_ACTIVE);
        return $query->getResult();

    }

    /////////////////////////////////////////////////////
    // MOVE FUNCTIONS TO INSTITUTION SPECIALIZATION

//     /**
//      * Get Specializations that have associated procedure types but are not yet
//      * linked to a specific institution.
//      *
//      * @param Institution $institution
//      * @return Doctrine\ORM\QueryBuilder
//      */
//     public function getQueryBuilderForUnselectedInstitutionSpecializations(Institution $institution)
//     {
//         $invalidSpecializationIds = array();
//         foreach ($institution->getInstitutionSpecializations() as $e) {
//             $invalidSpecializationIds[] = $e->getSpecialization()->getId();
//         }

//         foreach ($this->getIdsOfSpecializationsWithNoProcedureTypes() as $center) {
//             if (!in_array($center['id'], $invalidSpecializationIds)) {
//                 $invalidSpecializationIds[] = $center['id'];
//             }
//         }

//         $qb = $this->createQueryBuilder('a');
//         $qb->where('a.status = :active');

//         if (!empty($invalidSpecializationIds)) {
//             $qb->andWhere($qb->expr()->notIn('a.id', $invalidSpecializationIds));
//         }

//         $qb->orderBy('a.name', 'ASC')
//         ->setParameter('active', Specialization::STATUS_ACTIVE);

//         return $qb;
//     }

//     /**
//      * Get Specializations that are not yet linked to a specific institution excluding
//      * the medical centers with status InstitutionSpecializationGroupStatus::DRAFT
//      *
//      * @param Institution $institution
//      * @return Doctrine\ORM\QueryBuilder
//      */
//     public function getQueryBuilderForUnselectedInstitutionSpecializationsButWithDraftsIncluded(Institution $institution)
//     {
//         $usedSpecializationIds = array();
//         foreach ($institution->getInstitutionSpecializations() as $e) {
//             if ($e->getStatus() == InstitutionSpecializationGroupStatus::DRAFT) {
//                 continue;
//             }
//             $usedSpecializationIds[] = $e->getSpecialization()->getId();
//         }

//         $qb = $this->createQueryBuilder('a');
//         $qb->add('where', 'a.status = :active');
//         if (!empty($usedSpecializationIds)) {
//             $qb->andWhere($qb->expr()->notIn('a.id', $usedSpecializationIds));
//         }

//         $qb->orderBy('a.name', 'ASC')
//         ->setParameter('active', Specialization::STATUS_ACTIVE);

//         return $qb;
//     }

    //Get by slug or id
    public function getSpecialization($identifier)
    {
        if (is_numeric($identifier)) {
            return $this->find($identifier);
        } elseif (is_string($identifier)) {
            return $this->findOneBy(array('slug' => $identifier));
        }

        return null;
    }
}