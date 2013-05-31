<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;
class InstitutionTwigExtension extends \Twig_Extension
{
    /**
     * @var MediaExtension
     */
    private $mediaExtension;
    
    private $imagePlaceHolders = array();
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function setInstitutionService(InstitutionService $s)
    {
        $this->institutionService = $s;
    }
    
    public function setMediaExtension(MediaExtension $media)
    {
        $this->mediaExtension = $media;
    }
    
    public function setImagePlaceHolders($v)
    {
        $this->imagePlaceHolders = $v;
    }
    
    public function getFunctions()
    {
        return array(
            'render_institution_logo' => new \Twig_Function_Method($this, 'render_institution_logo'),
            'render_institution_contact_number' => new \Twig_Function_Method($this, 'render_institution_contact_number'),
            'render_institution_contact_details' => new \Twig_Function_Method($this, 'render_institution_contact_details'),
            'render_institution_suggestions' =>  new \Twig_Function_Method($this, 'render_institution_suggestions'),
            'render_institution_single_center_suggestions' =>  new \Twig_Function_Method($this, 'render_institution_single_center_suggestions'),
            'render_incomplete_clinic_profile' =>  new \Twig_Function_Method($this, 'render_incomplete_clinic_profile'),
            'render_institution_inquiries' =>  new \Twig_Function_Method($this, 'render_institution_inquiries'),
            'render_institution_unread_inquiries' =>  new \Twig_Function_Method($this, 'render_institution_unread_inquiries'),
            'render_institution_read_inquiries' =>  new \Twig_Function_Method($this, 'render_institution_read_inquiries')
        );
    }
    
    public function getName()
    {
        return 'institution_twig_extension';    
    }
    
    public function render_institution_suggestions(Institution $institution)
    {
        $suggestions = array();
        $isSingleCenter = $this->institutionService->isSingleCenter($institution);
        if(!$isSingleCenter && !$institution->getInstitutionMedicalCenters()) {
            $suggestions[] = array('description' => 'You currently have no centers for your Hospital yet.');
        }

        if($isSingleCenter && !$this->institutionService->getAllDoctors($institution)) {
            $suggestions[] = array('description' => 'You currently dont have doctors for your Hospital yet.');
        }
        
        if(!$institution->getDescription()) {
            $suggestions[] = array('description' => 'You currently have no description for your Hospital yet.');
        }
        
        if(!$institution->getLogo()) {
            $suggestions[] = array('description' => 'You currently have no logo for your Hospital yet.');
        }
        
        if(!$institution->getFeaturedMedia()) {
            $suggestions[] = array('description' => 'You currently have no banner for your Hospital yet.');
        }

        if(!$institution->getGallery()) {
            $suggestions[] = array('description' => 'You currently have no media gallery/photos for your Hospital yet.');
        }
        
        if(!$institution->getContactDetails()->count()) {
            $suggestions[] = array('description' => 'You currently have no contact details for your Hospital yet.');
        }
        
        if(!$institution->getSocialMediaSites()) {
            $suggestions[] = array('description' => 'You currently have no social media sites for your Hospital yet.');
        }
            
        if(!$this->institutionService->getAllGlobalAwards($institution)) {
            $suggestions[] = array('description' => 'You currently have no awards, certification, affiliations and accreditations for your Hospital yet.');
        }
        
        return $suggestions;
    }
    
    public function render_institution_single_center_suggestions(Institution $institution)
    {
        $suggestions = array();
        $medicalCenter = $this->institutionService->getFirstMedicalCenter($institution);
        
        if(!$this->institutionService->getAllDoctors($institution)) {
            $suggestions[] = array('description' => 'You currently dont have doctors for your Clinic yet.');
        }
        
        if(!$institution->getDescription()) {
            $suggestions[] = array('description' => 'You currently have no description for your Clinic yet.');
        }
        
        if(!$institution->getLogo()) {
            $suggestions[] = array('description' => 'You currently have no logo for your Clinic yet.');
        }
        
        if(!$institution->getFeaturedMedia()) {
            $suggestions[] = array('description' => 'You currently have no banner for your Clinic yet.');
        }
        
        if(!$institution->getGallery()) {
            $suggestions[] = array('description' => 'You currently have no media gallery/photos for your Clinic yet.');
        }
        
        if(!$institution->getContactDetails()->count()) {
            $suggestions[] = array('description' => 'You currently have no contact details for your Clinic yet.');
        }
        
        if(!$institution->getSocialMediaSites()) {
            $suggestions[] = array('description' => 'You currently have no social media sites for your Clinic yet.');
        }
        
        if(!$this->institutionService->getAllGlobalAwards($institution)) {
            $suggestions[] = array('description' => 'You currently have no awards, certification, affiliations and accreditations for your Hospital yet.');
        }
        
        return $suggestions;
    } 
    
    public function render_incomplete_clinic_profile(Institution $institution)
    {
        $incompleteClinics = array();
        $centers = $this->institutionService->getAllNotExpiredArchivedAndInactiveMedicalCenters($institution);
        foreach($centers as $each) {
            $emptyFields = $this->institutionService->getListOfEmptyFieldsOnInstitution($each);
            if(!empty($emptyFields)) {
                $incompleteClinics[] = array('name'=> $each->getName(), 'fields' => $emptyFields);
            }
        }
        
        return $incompleteClinics;
    }    
    
    public function render_institution_inquiries(Institution $institution)
    {
        $inquiries = $this->institutionService->getInstitutionInquiries($institution);

        return $inquiries;
    }
    
    public function render_institution_unread_inquiries(Institution $institution)
    {
        $unread_inquiries = $this->institutionService->getInstitutionInquiriesByStatus($institution, InstitutionInquiry::STATUS_UNREAD);
//         var_dump($unread_inquiries);exit;
        return $unread_inquiries;
    }
    
    public function render_institution_read_inquiries(Institution $institution)
    {
        $read_inquiries = $this->institutionService->getInstitutionInquiriesByStatus($institution, InstitutionInquiry::STATUS_READ);
        //         var_dump($unread_inquiries);exit;
        return $read_inquiries;
    }
    
    public function render_institution_contact_number(Institution $institution)
    {
        $contactNumber = \json_decode($institution->getContactNumber(), true);
        
        if (\is_null($contactNumber) || $contactNumber == '') {
            return null;
        }
        else {
            if (isset($contactNumber['country_code'])) {
                if (\preg_match('/^\+/', $contactNumber['country_code'])) {
                    $contactNumber['country_code'] = \preg_replace('/^\++/','+', $contactNumber['country_code']);
                }
                else {
                    // append + to country code
                    $contactNumber['country_code'] = '+'.$contactNumber['country_code'];
                }
                
                $result = \implode('-', $contactNumber);
                
            }else{
                if (isset($contactNumber['phone_number'])) {
                    if (\preg_match('/^\+/', $contactNumber['phone_number']['number'])) {
                            $result = \preg_replace('/^\++/','+', $contactNumber['phone_number']['number']);
                        }
                        else {
                            // append + to country code
                            $result = '+'.$contactNumber['phone_number']['number'];
                        }
                    }
            }
        }
        
        return $result;
    }
    public function render_institution_contact_details(Institution $institution)
    {
        $contactDetails = $this->institutionService->getContactDetailsByInstitution($institution);
        if (\is_null($contactDetails) || !$contactDetails) {
            return null;
        }
        else {
            $contactDetailsArray = array();
            foreach($contactDetails as $each) {
                if($each['type'] == 1) {
                    $contactDetailsArray[$each['type']] = array('type' => 'Phone', 'number' => $each['number']);
                }
                else if($each['type'] == 2) {
                    $contactDetailsArray[$each['type']] = array('type' => 'Mobile', 'number' => $each['number']);
                }
                else {
                    $contactDetailsArray[$each['type']] = array('type' => 'Fax', 'number' => $each['number']);
                }
            }
    
            return $result = $contactDetailsArray;
    
        }
    }
    
    public function render_institution_logo(Institution $institution, array $options = array())
    {

        if(!isset($options['attr']['class'])) {
            $options['attr']['class'] = '';
        }

        if(!isset($options['size'])) {
            $options['size'] = ImageSizes::MEDIUM;
        }

        if ($institution->getLogo()) {
            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institution->getLogo(), $options['size']);
            $html = '<img src="'.$mediaSrc.'" class="hospital-logo">';
        }
        else {
            $html = '<span class="hca-sprite hospital-default-logo '. $options['attr']['class'] .'"></span>';
        }

        return $html;
    }
}