<?php
namespace HealthCareAbroad\DoctorBundle\Repository;

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
        ->select('a, b')
        ->from('DoctorBundle:Doctor', 'a')
        ->leftJoin('a.specializations', 'b')
        ->where('a.status = :active')->setParameter('active', Doctor::STATUS_ACTIVE);;

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
    public function getDoctorsByInstitutionMedicalCenter($imcId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $query = "SELECT * FROM doctors a JOIN doctor_specializations b WHERE a.id = b.doctor_id AND b.specialization_id 
                    IN (SELECT specialization_id FROM institution_specializations WHERE institution_medical_center_id = :imcId) GROUP BY a.id";
        
        $stmt = $connection->prepare($query);
        $stmt->bindValue('imcId', $imcId);
        $stmt->execute();
        return $stmt->fetchAll();
        
    }
    
    public function getSpecializationListByMedicalSpecialist($doctorId)
    {
        $connection = $this->getEntityManager()->getConnection();
        // $query = "SELECT * FROM institution_medical_center_properties a JOIN offered_services b ON b.id = a.value WHERE a.institution_id = :id and a.institution_medical_center_id = :imcId";
    
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
    
    public function getAllDoctorsByInstitution($institution)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder()
        ->select('a, c, sp, dm')
        ->from('DoctorBundle:Doctor', 'a')
        ->innerJoin('a.specializations', 'sp')
        ->innerJoin('a.institutionMedicalCenters', 'c')
        ->leftJoin('a.media', 'dm')
        ->where('c.institution = :institutionId')
        ->andWhere('a.status = :status')
        ->orderBy('a.firstName')
        ->setParameter('institutionId', $institution->getId())
        ->setParameter('status', Doctor::STATUS_ACTIVE);
        
        return $qb;
        
    }
}