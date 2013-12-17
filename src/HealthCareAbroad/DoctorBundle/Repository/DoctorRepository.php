<?php
namespace HealthCareAbroad\DoctorBundle\Repository;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Doctrine\ORM\QueryBuilder;

use HealthCareAbroad\DoctorBundle\DoctorBundle;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;

class DoctorRepository extends EntityRepository
{
    public function getDoctorsByCriteria($criteria = array())
    {

        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('a, b, c')
        ->from('DoctorBundle:Doctor', 'a')
        ->leftJoin('a.specializations', 'b')
        ->leftJoin('b.medicalSpecialities', 'c');
        //->where('a.status = :active')->setParameter('active', Doctor::STATUS_ACTIVE);;

        foreach($criteria as $key => $value) {
            $qb->andWhere("a.$key = :$key")->setParameter($key, $value);
        }

        return $qb->getQuery()->getResult();
    }
    
    public function getDoctorsBySearchTerm($searchTerm)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('a')
        ->from('DoctorBundle:Doctor', 'a')
        ->where('a.status = :active')
        ->andWhere('a.firstName LIKE :searchTerm OR a.middleName LIKE :searchTerm OR a.lastName LIKE :searchTerm')
        ->setParameter('searchTerm', '%'.$searchTerm.'%')
        ->setParameter('active', Doctor::STATUS_ACTIVE)
        ->getQuery();
        
        return $query->getResult();
    }
    
    public function getDoctorsWithSpecialization()
    {
        $connection = $this->getEntityManager()->getConnection();
        $query = "SELECT * FROM doctors a JOIN doctor_specializations b ON b.doctor_id = a.id";
        $stmt = $connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get doctors of a  medical center
     * 
     * @param Mixed <InstitutionMedicalCenter, int> $institutionMedicalCenter
     * @author acgvelarde
     */
    public function findByInstitutionMedicalCenter($institutionMedicalCenter, $hydrationMode=Query::HYDRATE_OBJECT)
    {
        if ($institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            $institutionMedicalCenterId = $institutionMedicalCenter->getId();
        }
        else {
            if (\is_numeric($institutionMedicalCenter)) {
                $institutionMedicalCenterId = $institutionMedicalCenter;
            }
        }
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d, d_sp, d_medSp, d_medSp_sp, dm, dc')
            ->from('DoctorBundle:Doctor', 'd')
            ->innerJoin('d.institutionMedicalCenters', 'imc')
            ->leftJoin('d.specializations', 'd_sp')
            ->leftJoin('d.medicalSpecialities', 'd_medSp')
            ->leftJoin('d_medSp.specialization', 'd_medSp_sp')
            ->leftJoin('d.media', 'dm')
            ->leftJoin('d.contactDetails', 'dc')
            ->where('imc.id = :institutionMedicalCenterId')
                ->setParameter('institutionMedicalCenterId', $institutionMedicalCenterId);
        
        return $qb->getQuery()->getResult($hydrationMode);
    }
    
    public function getSpecializationListByMedicalSpecialist($doctorId)
    {
        $connection = $this->getEntityManager()->getConnection();
    
        $query = "SELECT a.name FROM specializations a JOIN doctor_specializations b ON b.specialization_id = a.id WHERE b.doctor_id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('id', $doctorId);
        $stmt->execute();
        //return $stmt->fetchAll();
        
        $specializations = array();
        foreach ($stmt->fetchAll() as $each) {
            $specializations[] = $each['name'];
        }
        
        $specializationsList = \implode("', '",$specializations);
        
        return $specializationsList;
        
    }
    
    public function getSpecializationByMedicalSpecialist($doctorId)
    {
        $connection = $this->getEntityManager()->getConnection();
       // $query = "SELECT * FROM institution_medical_center_properties a JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id and a.institution_medical_center_id = :imcId";
        
        $query = "SELECT * FROM specializations a JOIN doctor_specializations b ON b.specialization_id = a.id WHERE b.doctor_id = :id";
        $stmt = $connection->prepare($query);
        $stmt->bindValue('id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all doctors of an institution
     * 
     * @param Mixed <Institution, int> $institution
     * @return QueryBuilder
     */
    public function getAllDoctorsByInstitution($institution)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('a, sp, dm, ms')
        ->from('DoctorBundle:Doctor', 'a')
        ->innerJoin('a.institutionMedicalCenters', 'c')
        ->leftJoin('a.specializations', 'sp')
        ->leftJoin('a.medicalSpecialities', 'ms')
        ->leftJoin('a.media', 'dm')
        ->where('c.institution = :institution')
        ->andWhere('a.status = :status')
        ->andWhere('c.status = :centerStatus')
        ->orderBy('a.firstName')
        ->setParameter('institution', $institution)
        ->setParameter('status', Doctor::STATUS_ACTIVE)
        ->setParameter('centerStatus', InstitutionMedicalCenterStatus::APPROVED);
        
        return $qb;
        
    }
}