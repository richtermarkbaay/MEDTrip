<?php
/**
 * Doctor Service
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\DoctorBundle\Services;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;
class DoctorService
{
    
    protected $doctrine;
    
    protected $doctorMediaService;
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine, $doctorMediaService)
    {
        $this->doctrine = $doctrine;
        $this->doctorMediaService = $doctorMediaService;
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
    
    public function getDoctorsByCriteria($criteria = array())
    {
        return $this->doctrine->getRepository('DoctorBundle:Doctor')->getDoctorsByCriteria($criteria);
    }
    
    
    /**
     * @author Adelbert Silla
     * @param unknown_type $criteria
     * @param unknown_type $format
     * @return unknown|Ambigous <string, unknown, multitype:multitype:string NULL multitype:NULL   >
     */
    public function searchDoctors($criteria = array(), $format = null)
    {
        $doctors = $this->getDoctorsByCriteria($criteria);

        // Return Array Object Results
        if(!$format) {
            return $doctors;
        }

        // Convert Objects to MultiArray
        $doctorsResult = $this->doctorsObjectToArray($doctors);

        // Format Array to JSON
        if(strtolower($format) == 'json') {
            $doctorsResult = json_encode($doctorsResult);            
        }

        return $doctorsResult;
    }
    
    function doctorsObjectToArray($doctors)
    {
        $doctorsResult = array();
        foreach($doctors as $each) {
            $doctorsResult[$each->getId()] = $this->toArrayDoctor($each);
        }
        
        return $doctorsResult;
    }
    
    public function toArrayDoctor(Doctor $doctor)
    {
        $specializations = $contactDetails = array();

        foreach($doctor->getContactDetails() as $each) {
            $contactDetails[] = array(
                'id' => $each->getId(),
                'type' => $each->getType(),
                'number' => $each->getNumber(),
                'countryCode'=> $each->getCountryCode(),
                'areaCode' => $each->getAreaCode(),
                'abbr' => $each->getAbbr()
            );
        }

        foreach($doctor->getSpecializations() as $specialization) {
            $specializations[$specialization->getId()] = $specialization->getName();
        }

        $data = array(
            'id' => $doctor->getId(),
            'lastName' => $doctor->getLastName(),
            'firstName' => $doctor->getFirstName(),
            'middleName' => $doctor->getMiddleName(),
            'fullName' => $this->_getFullName($doctor),
            'contactEmail' => $doctor->getContactEmail(),
            'gender' => $doctor->getGender(),
            'suffix' => $doctor->getSuffix(),
            'contactDetails' => $contactDetails,
            'specializations' => $specializations,
            'mediaSrc' => ''
        );
        
        if($doctor->getMedia()) {
            $src = $this->doctorMediaService->mediaTwigExtension->getDoctorMediaSrc($doctor->getMedia(), ImageSizes::DOCTOR_LOGO);
            $data['mediaSrc'] = $src;
        }

        return $data;
    }
    
    private function _getFullName(Doctor $doctor)
    {
        $name = 'Dr.' . ucwords($doctor->getFirstName()) . ' ';
        if($doctor->getMiddleName()) {
            $name .= ucfirst(substr($doctor->getMiddleName(), 0, 1)) . '. ';
        }

        $name .= $doctor->getLastName();
        
        if($doctor->getSuffix()) {
            $name .= ' ' .$doctor->getSuffix();
        }
        
        return $name;
    }
}