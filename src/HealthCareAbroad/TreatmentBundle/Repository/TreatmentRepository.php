<?php
namespace HealthCareAbroad\TreatmentBundle\Repository;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use Doctrine\ORM\Query;

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

    public function getCountBySubSpecializationId($subSpecializationId) {
        $qb = $this->getEntityManager()->createQueryBuilder();
//         $qb->select('count(a)')
//             ->from('TreatmentBundle:Treatment', 'a')
//             ->where('a.status = :active')
//             ->andWhere('a.subSpecialization = :subSpecializationId')
//             ->setParameter('active', Treatment::STATUS_ACTIVE)
//             ->setParameter('subSpecializationId', $subSpecializationId);

        $qb->select('count(a)')
            ->from('TreatmentBundle:Treatment', 'a')
            ->innerJoin('a.subSpecializations', 'b', Join::WITH, 'b.id = :subSpecializationId')
            ->where('a.status = :active')
            ->setParameter('subSpecializationId', $subSpecializationId)
            ->setParameter('active', Treatment::STATUS_ACTIVE);


        $count = (int)$qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * Get QueryBuilder for getting active Treatments by Specialization
     *
     * @param Specialization $medicalCenter
     * @return QueryBuilder
     */
    public function getQueryBuilderForActiveTreatmentsBySpecialization(Specialization $specialization)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('TreatmentBundle:Treatment', 'a')
            ->innerJoin('a.specialization', 'b')
            ->where('b.id = :specialization')
            ->andWhere('a.status = :activeStatus')
            ->orderBy('a.name')
            ->setParameter('specialization', $specialization->getId())
            ->setParameter('activeStatus', Treatment::STATUS_ACTIVE);

        return $qb;
    }
    
    public function getQueryBuilderForActiveTreatmentsBySpecializationExcludingTreatment(Specialization $specialization, Treatment $currentTreatment)
    {
        $qb = $this->getQueryBuilderForActiveTreatmentsBySpecialization($specialization);
        
        // add condition where treatment id is not current treatment
        
        return $qb->andWhere('a.id != :treatment')
                  ->setParameter('treatment', $currentTreatment)->getQuery()->getResult();
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


    public function getBySpecializationId($specializationId)
    {
        $conn = $this->_em->getConnection();

        $sql = "SELECT a.*, d.id as subSpecializationId, d.name as subSpecializationName, d.description as subSpecializationDesc FROM treatments AS a " .
               "LEFT JOIN specializations AS b ON a.specialization_id = b.id " .
               "LEFT JOIN treatment_sub_specializations AS c ON a.id = c.treatment_id " .
               "LEFT JOIN sub_specializations AS d ON c.sub_specialization_id = d.id ".
               "WHERE a.specialization_id = :specializationId AND a.status = :treatmentStatus ".
                "ORDER BY d.name, a.name";

        $params = array(
            'specializationId' => $specializationId,
            'treatmentStatus' => Treatment::STATUS_ACTIVE
        );

        $result = $conn->executeQuery($sql, $params)->fetchAll(Query::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * Get Treatments By Specialization Id's
     * DEPRECATED - acgvelarde
     */
//     public function getByTreatmentBySpecializationId($specializations, $groupBySubSpecialization = false)
//     {
//         foreach ($specializations as $val){
//           $conn = $this->_em->getConnection();

//           $sql = "SELECT a.*, b.name as specializationName, b.id as specializationId, d.id as subSpecializationId, d.name as subSpecializationName, d.description as subSpecializationDesc FROM treatments AS a " .
//                           "LEFT JOIN specializations AS b ON a.specialization_id = b.id " .
//                           "LEFT JOIN treatment_sub_specializations AS c ON a.id = c.treatment_id " .
//                           "LEFT JOIN sub_specializations AS d ON c.sub_specialization_id = d.id ".
//                           "WHERE a.specialization_id = :id AND a.status = :treatmentStatus";
//           $params = array(
//                           'id' => $val->getSpecialization()->getID(),
//                           'treatmentStatus' => Treatment::STATUS_ACTIVE
//           );

//           $result[] = $conn->executeQuery($sql, $params)->fetchAll(Query::HYDRATE_ARRAY);
//         }
//         $selectedTreatments = array();
//         foreach ($val->getSpecialization()->getTreatments() as $treatId){
//             $selectedTreatments[] = $treatId->getID();
//         }

//         $treatments = array();
//         $specialization = array();
//         if(!$groupBySubSpecialization) {

//             $treatments = $result;

//         } else {
//              foreach($result as $array){
//                  foreach ($array as $each){

//                     if(!$each['subSpecializationId']) {
//                        $specialization['Other Treatments'][] = $each;
//                     } else {
//                        $specialization[$each['subSpecializationName']]['treatments'] = $each;
//                     }

//                     $treatments[$each['specializationName']] = $each;
//                     $treatments[$each['specializationName']]['subSpecializations'] = $specialization;
//                  }
//              }
//         }

//         return array('treatments' => $treatments ,'selectedTreatments' => $selectedTreatments ) ;
//     }
    //Get by slug or id
    public function getTreatment($identifier)
    {
        if (is_numeric($identifier)) {
            return $this->find($identifier);
        } elseif (is_string($identifier)) {
            return $this->findOneBy(array('slug' => $identifier));
        }

        return null;
    }
}