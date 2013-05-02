<?php
/**
 * Doctor Service
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\DoctorBundle\Services;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;
class DoctorService
{
    
    protected $doctrine;
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
        $this->doctrine = $doctrine;
    }
    
    public function getContactDetailsByDoctor(Doctor $doctor)
    {
        $connection = $this->doctrine->getEntityManager()->getConnection();
        $query = "SELECT * FROM contact_details a
        LEFT JOIN doctors_contact_details b ON a.id = b.contact_detail_id
        WHERE b.doctor_id = :doctorId";
    
        $stmt = $connection->prepare($query);
        $stmt->bindValue('doctorId', $doctor->getId());
        $stmt->execute();
    
        return $stmt->fetchAll();
    }
}