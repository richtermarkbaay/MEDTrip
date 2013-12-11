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
        if(!isset($criteria['status'])) {
            $criteria['status'] = Doctor::STATUS_ACTIVE;
        }

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
        $medicalSpecialities = $specializations = $contactDetails = array();

        foreach($doctor->getContactDetails() as $each) {
            $contactDetails[] = array(
                'id' => $each->getId(),
                'type' => $each->getType(),
                'number' => $each->getNumber(),
                'countryCode'=> $each->getCountryCode(),
                'areaCode' => $each->getAreaCode(),
                'ext' => $each->getExt(),
                'abbr' => $each->getAbbr()
            );
        }
        
        foreach($doctor->getSpecializations() as $specialization) {
            $specializations[$specialization->getId()] = $specialization->getName();
        }

        foreach($doctor->getMedicalSpecialities() as $each) {
            $medicalSpecialities[$each->getSpecialization()->getId()][$each->getId()] = $each->getName();
        }

        $data = array(
            'id' => $doctor->getId(),
            'lastName' => $doctor->getLastName(),
            'firstName' => $doctor->getFirstName(),
            'middleName' => $doctor->getMiddleName(),
            'fullName' => self::getFullName($doctor),
            'contactEmail' => $doctor->getContactEmail(),
            'gender' => $doctor->getGender(),
            'suffix' => $doctor->getSuffix(),
            'contactDetails' => $contactDetails,
            'specializations' => $specializations,
            'medicalSpecialities' => $medicalSpecialities,
            'specialitiesString' => self::doctorSpecialitiesToString($doctor),
            'mediaSrc' => ''
        );

        if($doctor->getMedia()) {
            $src = $this->doctorMediaService->mediaTwigExtension->getDoctorMediaSrc($doctor->getMedia(), ImageSizes::DOCTOR_LOGO);
            $data['mediaSrc'] = $src;
        }

        return $data;
    }
    
    static function doctorSpecialitiesToString($doctor)
    {   
        $specializations = $medicalSpecialities = array();

        if(is_object($doctor)) {
            foreach($doctor->getSpecializations() as $specialization) {
                $specializations[$specialization->getId()] = $specialization->getName();
            }
            foreach($doctor->getMedicalSpecialities() as $each) {
                $medicalSpecialities[$each->getSpecialization()->getId()][$each->getId()] = $each->getName();
            }
        } else if(is_array($doctor)) {
            foreach($doctor['specializations'] as $each) {
                $specializations[$each['id']] = $each['name'];
            }

            foreach($doctor['medicalSpecialities'] as $each) {
                if(isset($each['specialization']))
                    $medicalSpecialities[$each['specialization']['id']][$each['id']] = $each['name'];
            }
        }

        if(empty($specializations)) {
            return '';
        }

        $specializaties = array_replace($specializations, $medicalSpecialities);

        $string = '';
        foreach($specializaties as $each) {
            $string .= (is_array($each) ? implode(', ', array_values($each)) : $each) . ', ';  
        }

        return substr($string, 0, -2);
    }
    
    static function getFullName(Doctor $doctor)
    {
        $name = 'Dr. ' . ucwords($doctor->getFirstName()) . ' ';
        if($doctor->getMiddleName()) {
            $name .= ucfirst(substr($doctor->getMiddleName(), 0, 1)) . '. ';
        }

        $name .= ucwords($doctor->getLastName());
        
        if($doctor->getSuffix()) {
            $name .= ' ' .$doctor->getSuffix();
        }
        
        return $name;
    }
}