<?php
namespace HealthCareAbroad\TreatmentBundle\Repository;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * TreatmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TreatmentRepository extends EntityRepository
{
    public function search($term = '', $limit = 10)
    {
        $dql = "
            SELECT p
            FROM TreatmentBundle:Treatment AS p
            WHERE p.name LIKE :term
            ORDER BY p.name ASC"
        ;

        $query = $this->_em->createQuery($dql);
        $query->setParameter('term', "%$term%");
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    public function getCountByTreatmentId($treatmentId) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(a)')
            ->from('TreatmentBundle:Treatment', 'a')
            ->where('a.status = :active')
            ->andWhere('a.treatment = :treatmentId')
            ->setParameter('active', Treatment::STATUS_ACTIVE)
            ->setParameter('treatmentId', $treatmentId);

        $count = (int)$qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * Get QueryBuilder for getting active Treatments by Specialization
     *
     * @param Specialization $medicalCenter
     * @return QueryBuilder
     */
    public function getQueryBuilderForActiveTreatmentsBySpecialization(Specialization $medicalCenter)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('TreatmentBundle:Treatment', 'a')
            ->innerJoin('a.treatment', 'b')
            ->where('b.medicalCenter = :medicalCenterId')
            ->andWhere('a.status = :activeStatus')
            ->orderBy('a.treatment, a.name')
            ->setParameter('medicalCenterId', $medicalCenter->getId())
            ->setParameter('activeStatus', Treatment::STATUS_ACTIVE);

        return $qb;
    }

    //////////////////////////////////////////////////////////////////////////
    // MOVE TO INSTITUTION TREATMENTS

    /**
     * Get query builder for getting available TreatmentProcedures that can be used by InstitutionTreatment
     *
     * @param InstitutionTreatment $institutionTreatment
     */
//     public function getQueryBuilderForAvailableInstitutionTreatmentProcedures(InstitutionTreatment $institutionTreatment)
//     {
//         /**
//          SELECT a . * , b . *
//         FROM `treatment_procedures` a
//         LEFT JOIN `institution_treatment_procedures` b ON a.id = b.treatment_procedure_id
//         AND b.institution_treatment_id =2
//         WHERE a.treatment_id =7
//         AND b.id IS NULL
//          **/

//         // create query builder similar to above raw sql
//         $qb = $this->getEntityManager()->createQueryBuilder();
//         $qb->select('a')
//             ->from('TreatmentBundle:TreatmentProcedure', 'a')
//             ->leftJoin('a.institutionTreatmentProcedures', 'b', Join::WITH, 'b.institutionTreatment = :institutionTreatmentId')
//             ->where('a.status = :active')
//             ->andWhere('a.treatment = :treatmentId')
//             ->andWhere('b.id IS NULL')
//             ->setParameter('active', TreatmentProcedure::STATUS_ACTIVE)
//             ->setParameter('treatmentId', $institutionTreatment->getTreatment()->getId())
//             ->setParameter('institutionTreatmentId', $institutionTreatment->getId());
//         return $qb;
//     }
}