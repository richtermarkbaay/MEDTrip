<?php
namespace HealthCareAbroad\DoctorBundle\Twig;

use HealthCareAbroad\DoctorBundle\Services\DoctorService;

/**
 * Twig extension class for functionalities relating to a doctor
 * 
 * @author Allejo Chris G. Velarde
 */
use HealthCareAbroad\DoctorBundle\Entity\Doctor;

class DoctorTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'doctorToArray' => new \Twig_Function_Method($this, 'doctorToArray'),
            'institutionMedicalCenterGroupDoctorList' => new \Twig_Function_Method($this, 'institutionMedicalCenterGroupDoctorList'),
            'doctor_specializations_to_string' => new \Twig_Function_Method($this, 'doctorSpecializationsToString')
        );
    }
    
    public function getName()
    {
        return 'doctorTwigExtension';
    }
    
    public function doctorToArray(Doctor $doctor)
    {
        $arr = array(
            'id' => $doctor->getId(),
            'name' => "{$doctor->getFirstName()} {$doctor->getMiddleName()} {$doctor->getLastName()}",
            'medicalCenters' => array()
        );
        foreach($doctor->getMedicalCenters() as $dmc) {
            $arr['medicalCenters'][$dmc->getId()] = $dmc->getName();
        }

        return $arr;
    }
    
    public function doctorSpecializationsToString($doctor) 
    {
        return DoctorService::doctorSpecialitiesToString($doctor);
    }
}