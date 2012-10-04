<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityRepository;

/**
 * InstitutionMedicalCenterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstitutionMedicalCenterRepository extends EntityRepository
{
    public function getMedicalCentersByTreatment(Treatment $procedureType, MedicalProcedure $procedure = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('a')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->leftJoin('a.medicalCenter', 'b')
            ->leftJoin('b.treatments', 'c');

        if ($procedure) {
            $qb->leftJoin('c.treatmentProcedures', 'd')
                ->where('d = :procedure')
                ->setParameter('procedure', $procedure);
        }
        else {
            $qb->where('c = :procedureType')
                ->setParameter('procedureType', $procedureType);
        }

        $qb->orderBy('b.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getMedicalCentersByCountry(Country $country)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('a')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->leftJoin('a.institution', 'b')
            ->leftJoin('a.medicalCenter', 'c')
            ->where('b.country = :countryId')
            ->andWhere('a.status = :status')
            ->setParameter('countryId', $country->getId())
            ->setParameter('status', InstitutionMedicalCenterStatus::APPROVED)
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getMedicalCentersByCity(City $country)
    {
        $qb = $this->_em->createQueryBuilder()
        ->select('a')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->leftJoin('a.institution', 'b')
        ->leftJoin('a.medicalCenter', 'c')
        ->where('b.country = :countryId')
        ->andWhere('a.status = :status')
        ->setParameter('countryId', $country->getId())
        ->setParameter('status', InstitutionMedicalCenterStatus::APPROVED)
        ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getCountByMedicalCenterId($medicalCenterId) {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('count(a)')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->andWhere('a.medicalCenter = :medicalCenterId')
        ->setParameter('medicalCenterId', $medicalCenterId);

        $count = (int)$qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    function getProcedureTypeIdsWithProcedure($medicalCenterId)
    {
        $conn = $this->_em->getConnection();
        $qry = "SELECT a.medical_procedure_type_id, b.id FROM institution_medical_procedure_types AS a " .
                "JOIN medical_procedures AS b ON a.medical_procedure_type_id = b.medical_procedure_type_id " .
                "JOIN institution_treatment_procedures AS c ON b.id = c.medical_procedure_id ".
                "WHERE institution_medical_center_id = $medicalCenterId AND b.status = 1 AND c.status = 1 " .
                "GROUP BY a.medical_procedure_type_id";

        $result = $conn->executeQuery($qry)->fetchAll();

        $ids = array();
        foreach($result as $each) {
            $ids[] = (int)$each['medical_procedure_type_id'];
        }

        return $ids;
    }

    public function getMedicalCentersList($institutionId)
    {
        $qb = $this->_em->createQueryBuilder()
        ->select('b.id, b.name')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->leftJoin('a.medicalCenter', 'b')
        ->add('where','a.institution = :institution')
        ->setParameter('institution', $institutionId)
        ->orderBy('b.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the available MedicalProcedure type of a MedicalCenter that has not been used in the Institution
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function getAvailableTreatments(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        //$dql = "SELECT p FROM InstitutionBundle:InstitutionMedicalCenter"
        $sql = "SELECT a.* FROM medical_procedure_types a, institution_medical_centers b ".
               "WHERE b.medical_center_id = :medical_center_id ".
               "AND b.institution_id = :institution_id ".
               "AND a.medical_center_id = b.medical_center_id ".
            "AND a.status = :active_medical_procedure_type ".
            "AND a.id NOT IN (SELECT i.medical_procedure_type_id FROM institution_medical_procedure_types i WHERE i.institution_medical_center_id = b.id)";

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult("MedicalProcedureBundle:Treatment", "a")
            ->addFieldResult("a", "id", "id")
            ->addFieldResult("a", "medicalCenter", "medical_center_id")
            ->addFieldResult("a", "name", "name")
            ->addFieldResult("a", "description", "description")
            ->addFieldResult("a", "dateModified", "date_modified")
            ->addFieldResult("a", "dateCreated", "date_created")
            ->addFieldResult("a", "slug", "slug")
            ->addFieldResult("a", "status", "status");


        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter('medical_center_id', $institutionMedicalCenter->getMedicalCenter()->getId())
            ->setParameter('institution_id', $institutionMedicalCenter->getInstitution()->getId())
            ->setParameter('active_medical_procedure_type', Treatment::STATUS_ACTIVE);

        return $query->getResult();
    }

    private function _getCommonRSM()
    {

    }
}