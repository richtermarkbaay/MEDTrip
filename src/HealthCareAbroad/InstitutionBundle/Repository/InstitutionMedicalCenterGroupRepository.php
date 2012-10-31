<?php
namespace HealthCareAbroad\InstitutionBundle\Repository;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\TreatmentBundle\Entity\MedicalCenter;

use Doctrine\ORM\QueryBuilder;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroup;

use Doctrine\ORM\EntityRepository;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenterGroupRepository extends EntityRepository
{
    /**
     * Find Doctors that has specializations matching $institutionMedicalCenterGroup by search keyword $searchKey
     * 
     * @param InstitutionMedicalCenterGroup $institutionMedicalCenterGroup
     * @param string $searchKey
     */
    public function findAvailableDoctorBySearchKey(InstitutionMedicalCenterGroup $institutionMedicalCenterGroup, $searchKey='')
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
            ->setParameter('imcgId', $institutionMedicalCenterGroup->getId())
            ->setParameter('activeStatus', 1)
            ->setParameter('searchKey', '%'.$searchKey.'%');
        
        return $qb->getQuery()->getResult();
    }
}