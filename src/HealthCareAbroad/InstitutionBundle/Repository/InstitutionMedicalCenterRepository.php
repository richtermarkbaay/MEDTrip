<?php

namespace HealthCareAbroad\InstitutionBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\TreatmentBundle\Entity\TreatmentProcedure;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;
use HealthCareAbroad\TreatmentBundle\Entity\MedicalCenter;

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

    /** TODO - Verify Method! - Moved from medicalCenterGroupRepo
     * Find Doctors that has specializations matching $institutionMedicalCenter by search keyword $searchKey
     *
     * @param InstitutionMedicalCenterGroup $institutionMedicalCenter
     * @param string $searchKey
     */
    public function findAvailableDoctorBySearchKey(InstitutionMedicalCenter $institutionMedicalCenter, $searchKey='')
    {
        /**
         SELECT d.*
         FROM `doctors` d
         INNER JOIN `doctor_to_medical_centers` dmc ON d.id = dmc.doctor_id
         INNER JOIN `institution_medical_centers` imc ON dmc.medical_center_id = imc.medical_center_id AND imc.institution_medical_center_group_id = 1
         WHERE
         d.status = 1

         SELECT d0_.id AS id0, d0_.first_name AS first_name1, d0_.middle_name AS middle_name2, d0_.last_name AS last_name3, d0_.date_created AS date_created4, d0_.status AS status5
         FROM doctors d0_
         INNER JOIN doctor_to_medical_centers d2_ ON d0_.id = d2_.doctor_id
         INNER JOIN medical_centers m1_ ON m1_.id = d2_.medical_center_id
         INNER JOIN institution_medical_centers i3_ ON m1_.id = i3_.medical_center_id AND (i3_.institution_medical_center_group_id = 1)
         LEFT JOIN institution_medical_center_group_doctors i5_ ON d0_.id = i5_.doctor_id
         LEFT JOIN institution_medical_center_groups i4_ ON i4_.id = i5_.institution_medical_center_group_id AND (i4_.id = 1)
         WHERE d0_.status = 1 AND i4_.id IS NULL
         */
        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('d, dmc')
        ->from('DoctorBundle:Doctor', 'd')
        ->innerJoin('d.medicalCenters', 'dmc')
        ->innerJoin('dmc.institutionMedicalCenters', 'imc', Join::WITH, 'imc.institutionMedicalCenterGroup = :imcgId')
        ->leftJoin('d.institutionMedicalCenterGroups', 'imcg', Join::WITH, 'imcg.id = :imcgId')
        ->where('d.status = :activeStatus')
        ->andWhere('imcg.id IS NULL')
        ->andWhere('d.firstName LIKE :searchKey OR d.middleName LIKE :searchKey OR d.lastName LIKE :searchKey')
        ->setParameter('imcgId', $institutionMedicalCenter->getId())
        ->setParameter('activeStatus', 1)
        ->setParameter('searchKey', '%'.$searchKey.'%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get QueryBuilder for getting all medical centers of an institution
     *
     * @param Institution $institution
     * @return QueryBuilder
     */
    public function getInstitutionMedicalCentersQueryBuilder(Institution $institution)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->where('a.institution = :institutionId')
            ->orderBy('a.name')
            ->setParameter('institutionId', $institution->getId());

        return $qb;
    }

    /**
     * Get medicaCenter count by Institution
     *
     * @param Institution $institution
     * @return int
     */
    public function getCountByInstitution(Institution $institution)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('count(a)')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
        ->where('a.institution = :institutionId')
        ->setParameter('institutionId', $institution->getId());
        $count = $qb->getQuery()->getSingleScalarResult();

        return $count;
    }

    private function _getCommonRSM()
    {

    }

    // --- The following functions are used on the frontend and will return results
    // --- where institution has ACTIVE and APPROVED statuses and/or where the
    // --- institution medical center has APPROVED status

//     public function getMedicalCentersByCountry(\HealthCareAbroad\HelperBundle\Entity\Country $country)
//     {
//         $query = $this->getEntityManager()->createQuery('
//                 SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
//                 LEFT JOIN a.institution b
//                 WHERE b.country = :country
//                 AND a.status = :imcStatus
//                 AND b.status = :institutionStatus')
//             ->setParameter('country', $country)
//             ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
//             ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

//         return $query->getResult();
//     }

//     public function getMedicalCentersByCity(\HealthCareAbroad\HelperBundle\Entity\City $city)
//     {
//         $query = $this->getEntityManager()->createQuery('
//                 SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
//                 LEFT JOIN a.institution b
//                 WHERE b.city = :city
//                 AND b.city IS NOT NULL
//                 AND a.status = :imcStatus
//                 AND b.status = :institutionStatus')
//             ->setParameter('city', $city)
//             ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
//             ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

//         return $query->getResult();
//     }

    public function getMedicalCentersBySpecialization($specialization)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN a.institution c
                WHERE b.specialization = :specialization
                AND c.status = :institutionStatus
                AND a.status = :imcStatus')
            ->setParameter('specialization', $specialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);
        return $query->getResult();
    }

    public function getMedicalCentersBySpecializationAndCountry($specialization, $country)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN a.institution c
                WHERE b.specialization = :specialization
                AND c.status = :institutionStatus
                AND c.country = :country
                AND a.status = :imcStatus')
            ->setParameter('specialization', $specialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->setParameter('country', $country);

        return $query->getResult();
    }

    public function getMedicalCentersBySpecializationAndCity($specialization, $city)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN a.institution c
                WHERE b.specialization = :specialization
                AND c.status = :institutionStatus
                AND c.city = :city
                AND a.status = :imcStatus')
            ->setParameter('specialization', $specialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->setParameter('city', $city);

        return $query->getResult();
    }

    public function getMedicalCentersBySubSpecializationAndCountry($subSpecialization, $country)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN c.subSpecializations d
                LEFT JOIN a.institution e
                WHERE d.id = :subSpecialization
                AND e.status = :institutionStatus
                AND e.country = :country
                AND a.status = :imcStatus')
            ->setParameter('subSpecialization', $subSpecialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->setParameter('country', $country);

        return $query->getResult();
    }

    public function getMedicalCentersBySubSpecializationAndCity($subSpecialization, $city)
    {
        $query = $this->getEntityManager()
        ->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN c.subSpecializations d
                LEFT JOIN a.institution e
                WHERE d.id = :subSpecialization
                AND e.city = :city
                AND e.status = :institutionStatus
                AND a.status = :imcStatus')
            ->setParameter('subSpecialization', $subSpecialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED)
            ->setParameter('city', $city);

        return $query->getResult();
    }

    public function getMedicalCentersByTreatmentAndCountry($treatment, $country)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN a.institution d
                WHERE c.id = :treatment
                AND d.country = :country
                AND a.status = :imcStatus')
            ->setParameter('treatment', $treatment)
            ->setParameter('country', $country)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

        return $query->getResult();
    }

    public function getMedicalCentersByTreatmentAndCity($treatment, $city)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN a.institution d
                WHERE c.id = :treatment
                AND d.status = :institutionStatus
                AND d.city = :city
                AND a.status = :imcStatus')
            ->setParameter('treatment', $treatment)
            ->setParameter('city', $city)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);;

        return $query->getResult();
    }

    public function getMedicalCentersBySubSpecialization($subSpecialization)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN c.subSpecializations d
                LEFT JOIN a.institution e
                WHERE d.id = :subSpecialization
                AND e.status = :institutionStatus
                AND a.status = :imcStatus')
            ->setParameter('subSpecialization', $subSpecialization)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

        return $query->getResult();
    }

    public function getMedicalCentersByTreatment($treatment)
    {
        $query = $this->getEntityManager()->createQuery('
                SELECT a FROM InstitutionBundle:InstitutionMedicalCenter a
                INNER JOIN a.institutionSpecializations b
                LEFT JOIN b.treatments c
                LEFT JOIN a.institution d
                WHERE c.id = :treatment
                AND d.status = :institutionStatus
                AND a.status = :imcStatus')
            ->setParameter('treatment', $treatment)
            ->setParameter('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus())
            ->setParameter('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

        return $query->getResult();
    }
}