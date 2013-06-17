<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;
class InstitutionTwigExtension extends \Twig_Extension
{
    const ADS_CONTEXT = 4;

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
            'render_institution_read_inquiries' =>  new \Twig_Function_Method($this, 'render_institution_read_inquiries'),
            'contact_label_type' =>   new \Twig_Function_Method($this, 'contact_label_type')
        );
    }
    
    public function getName()
    {
        return 'institution_twig_extension';    
    }
    
    public function contact_label_type($contactType)
    {
        if($contactType == 1) {
            $label = 'Phone Number';
        }
        else if($contactType == 2) {
            $label = 'Mobile Number';
        }
        else {
            $label = 'Fax Number';
        }
        
        return $label;
    }
    
    public function render_institution_suggestions(Institution $institution)
    {
        $suggestions = array();
        $isSingleCenter = $this->institutionService->isSingleCenter($institution);
        
        $label = ($isSingleCenter ? 'clinic' : 'hospital');
        
        if(!$isSingleCenter && !$institution->getInstitutionMedicalCenters()) {
            $suggestions[] = array('description' => 'You currently have no centers for your '.$label.' yet.');
        }

        if($isSingleCenter && !$this->institutionService->getAllDoctors($institution)) {
            $suggestions[] = array('description' => 'You currently dont have doctors for your '.$label.' yet.');
        }
        
        if(!$institution->getDescription()) {
            $suggestions[] = array('description' => 'You currently have no description for your '.$label.' yet.');
        }
        
        if(!$institution->getLogo()) {
            if($institution->getPayingClient()){
                $suggestions[] = array('description' => 'You have not yet added a logo. Upload it today and help patients make an instant brand connection between your clinic and the treatments you offer.');
            }else{
                $suggestions[] = array('description' => 'Upgrade your listing today and have your logo show on your '.($isSingleCenter ? 'clinic page' : 'clinic pages').'. ');
            }    
        }
        
        if(!$institution->getFeaturedMedia()) {
            if($institution->getPayingClient()){
                $suggestions[] = array('description' => 'You have not yet uploaded your header image. Make use of this space to add a large image to establish your brand and reputation.');
            }else{
                $suggestions[] = array('description' => 'Upgrade your listing today and have a header image show on your '.($isSingleCenter ? 'clinic page' : 'clinic pages').'. Making use of this space to add a large image helps in establishing your brand and reputation.');
            }
        }

        if(!$institution->getGallery()) {
             if($institution->getPayingClient()){
                $suggestions[] = array('description' => 'You have not yet uploaded photos or videos. Beautiful photos and videos help give users a more complete image of your '.$label.', and makes decisions easier and more likely.');
            }else{
                $suggestions[] = array('description' => 'Upgrade your listing today to add photos and videos. Beautiful photos and videos help give users a more complete image of your '.$label.', and makes decisions easier and more likely.');
            }
        }
        
        if(!$institution->getContactDetails()->count()) {
            $suggestions[] = array('description' => 'You currently have no contact details for your '.$label.' yet.');
        }
        
        if(!$institution->getSocialMediaSites()) {
            $suggestions[] = array('description' => 'You currently have no social media sites for your '.$label.' yet.');
        }
            
        if(!$this->institutionService->getAllGlobalAwards($institution)) {
            $suggestions[] = array('description' => 'You have not yet listed any awards, certifications, affiliations or accreditations. List your certifications and accreditations to help show that your clinics adhere to international standards of quality; list your awards and affiliations to show you are recognized by peers in your industry. ');
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
                $incompleteClinics[] = array('id' => $each->getId(), 'name'=> $each->getName(), 'fields' => $emptyFields);
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
        
        return $unread_inquiries;
    }
    
    public function render_institution_read_inquiries(Institution $institution)
    {
        $read_inquiries = $this->institutionService->getInstitutionInquiriesByStatus($institution, InstitutionInquiry::STATUS_READ);
        
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
        $contactDetails = $institution->getContactDetails();

        $contactDetailsArray = array();
        foreach($contactDetails as $each) {
            $contactDetailsArray[$each['type']] = array('type' => ContactDetailTypes::getTypeLabel($each['type']), 'number' => $each['number']);
        }

        return $result = $contactDetailsArray;
    }
    
    public function render_institution_logo(Institution $institution, array $options = array())
    {
        $defaultOptions = array(
                        'attr' => array(),
                        'media_format' => 'default',
                        'placeholder' => ''
        );
        $options = \array_merge($defaultOptions, $options);
        $html = '';
        
        // TODO - Institution Logo for non-paying client is temporarily enabled in ADS section.
        $isAdsContext = isset($options['context']) && $options['context'] == self::ADS_CONTEXT;
        
        if (($institutionLogo = $institution->getLogo()) && ($institution->getPayingClient() || $isAdsContext)) {
            if(isset($options['attr']['class']))
                $options['attr']['class'] .= ' hospital-logo';
            else 
                $options['attr']['class'] = 'hospital-logo';

            if(!isset($options['size'])) {
                $options['size'] = ImageSizes::MEDIUM;
            }    

            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institution->getLogo(), $options['size']);
            $html = '<img src="'.$mediaSrc.'" class="hospital-logo">';

        } else {
            if(isset($options['attr']['class'])) {
                $html = '<span class="hca-sprite hospital-default-logo '. $options['attr']['class'] .'"></span>';
            }
            else {
                $html = '<span class="hca-sprite hospital-default-logo"></span>';
            }
            //$html = '<span class="hca-sprite hospital-default-logo '. isset($options['attr']['class']) ? $options['attr']['class'] : '' .'"></span>';
        }

        return $html;
    }
}