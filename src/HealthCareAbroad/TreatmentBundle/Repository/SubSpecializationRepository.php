<?php

namespace HealthCareAbroad\TreatmentBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityRepository;

/**
 * SubSpecializationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubSpecializationRepository extends EntityRepository
{
    public function search($term = '', $limit = 10)
    {
        $dql = "
            SELECT c
            FROM TreatmentBundle:SubSpecialization AS c
            WHERE c.name LIKE :term
            ORDER BY c.name ASC"
        ;

        $query = $this->_em->createQuery($dql);
        $query->setParameter('term', "%$term%");
        $query->setMaxResults($limit);

        return $query->getResult();
    }

    /**
     * Get QueryBuilder for getting active treatments that can be used for dropdown field types
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForGettingAvailableSubSpecializations()
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->add('select', 't')
            ->add('from', 'TreatmentBundle:SubSpecialization t')
            ->add('where', 't.status = :active')
            ->setParameter('active', SubSpecialization::STATUS_ACTIVE);
    }


    //////////////////////////////////////////////////////////////////////////////
    // MOVE TO INSTITUTION SPECIALIZATIONS

//     /**
//      * @author Adelbert Silla
//      * @param InstitutionSpecialization $institutionSpecialization
//      * @return array of treatment_procedure type_id
//      */
//     public function getActiveProcedureTypeIdsOfInstitution(InstitutionSpecialization $institutionSpecialization)
//     {
//         $qb = $this->_em->createQueryBuilder()
//             ->select('a.id, b.id as treatment_id')
//             ->from('InstitutionBundle:InstitutionTreatment', 'a')
//             ->innerJoin('a.treatment', 'b')
//             ->add('where','a.institutionSpecialization = :institutionSpecialization')
//             ->setParameter('institutionSpecialization', $institutionSpecialization);

//         $result = $qb->getQuery()->getResult();
//         $ids = array();

//         foreach($result as $each) {
//             $ids[$each['id']] = $each['treatment_id'];
//         }

//         return $ids;
//     }

//     /**
//      * @author Adelbert Silla
//      *
//      * Get the query builder available MedicalProcedure type of a Specialization that has not been used in the Institution
//      *
//      * @param InstitutionSpecialization $institutionSpecialization
//      */
//     public function getQueryBuilderForAvailableInstitutionTreatments(InstitutionSpecialization $institutionSpecialization)
//     {
//         /**
//         SELECT a . * , b . *
//         FROM `treatments` a
//         LEFT JOIN `institution_treatments` b ON a.id = b.treatment_id
//         WHERE a.medical_center_id =7
//         AND b.id IS NULL
//          **/

//         $qb = $this->getEntityManager()->createQueryBuilder();
//         $qb->select('a')
//             ->from('TreatmentBundle:Treatment', 'a')
//             ->leftJoin('a.institutionTreatments', 'b')
//             ->where('a.status = :active')
//             ->andWhere('a.medicalCenter = :medicalCenterId')
//             ->andWhere('b.id IS NULL')
//             ->setParameter('active', Treatment::STATUS_ACTIVE)
//             ->setParameter('medicalCenterId', $institutionSpecialization->getSpecialization()->getId());
//         return $qb;


//         /**$activeProcedureTypeIds = $this->getActiveProcedureTypeIdsOfInstitution($institutionSpecialization);

//         return $this->getEntityManager()->createQueryBuilder()
//             ->add('select', 'a')
//             ->add('from', 'TreatmentBundle:Treatment a')
//             ->add('where', 'a.medicalCenter = :medicalCenter')
//             ->andWhere('a.id NOT IN (:activeProcedureTypeIds)')
//             ->andWhere('a.status = :active')
//             ->setParameter('medicalCenter', $institutionSpecialization->getSpecialization())
//             ->setParameter('activeProcedureTypeIds', implode(',', $activeProcedureTypeIds))
//             ->setParameter('active', Treatment::STATUS_ACTIVE);**/
//     }
}