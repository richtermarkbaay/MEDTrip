<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionGalleryService;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

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
    

    /** 
     * @var InstitutionGalleryService
     */
    private $institutionGalleryService;
    
    
    public function setInstitutionService(InstitutionService $s)
    {
        $this->institutionService = $s;
    }
    
    /*
     * @var InstitutionMedicalCenterService
     */
    private $institutionMedicalCenterService;
    
    public function setInstitutionMedicalCenterService(InstitutionMedicalCenterService $imcService)
    {
        $this->institutionMedicalCenterService = $imcService;
    }
    
    public function setMediaExtension(MediaExtension $media)
    {
        $this->mediaExtension = $media;
    }
    
    public function setInstitutionGalleryService(InstitutionGalleryService $service)
    {
        $this->institutionGalleryService = $service;
    }
    
    public function setImagePlaceHolders($v)
    {
        $this->imagePlaceHolders = $v;
    }
    
    public function getFunctions()
    {
        return array(
            'render_institution_logo' => new \Twig_Function_Method($this, 'renderInstitutionLogo'),
            'render_institution_contact_details' => new \Twig_Function_Method($this, 'renderInstitutionContactDetails'),
            'render_institution_suggestions' =>  new \Twig_Function_Method($this, 'render_institution_suggestions'),
            'render_institution_single_center_suggestions' =>  new \Twig_Function_Method($this, 'render_institution_single_center_suggestions'),
            'render_incomplete_clinic_profile' =>  new \Twig_Function_Method($this, 'render_incomplete_clinic_profile'),
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
            $suggestions[] = array('description' => '<span class="span1"><i class="icon-medkit icon-2x hca-red pull-left"></i></span>You currently have no centers for your '.$label.' yet.');
        }
        
        if($isSingleCenter) {
            $medicalCenter = $this->institutionService->getFirstMedicalCenter($institution);;
            if(!$medicalCenter->getDoctors()->count()) {
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-user-md icon-2x hca-red pull-left"></i></span>You currently dont have doctors for your '.$label.' yet.');
            }
        }
        
        if(!$institution->getDescription()) {
            $suggestions[] = array('description' => '<span class="span1"><i class="icon-file icon-2x hca-red pull-left"></i></span>You currently have no <b>description</b> for your '.$label.' yet.');
        }
        
        if(!$institution->getLogo()) {
            if($institution->getPayingClient()){
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-h-sign icon-2x hca-red pull-left"></i></span> You have not yet added a <b>logo</b>. Upload it today and help patients make an instant brand connection between your clinic and the treatments you offer.');
            }else{
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-h-sign icon-2x hca-red pull-left"></i></span>Upgrade your listing today and have your <b>logo</b> show on your '.($isSingleCenter ? 'clinic page' : 'clinic pages').'. ');
            }    
        }
        
        if(!$institution->getFeaturedMedia()) {
            if($institution->getPayingClient()){
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-picture icon-2x hca-red pull-left"></i></span>You have not yet uploaded your <b>cover photo</b>. Make use of this space to add a large image to establish your brand and reputation.');
            }else{
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-picture icon-2x hca-red pull-left"></i></span>Upgrade your listing today and have a <b>cover photo</b> show on your '.($isSingleCenter ? 'clinic page' : 'clinic pages').'. Making use of this space to add a large image helps in establishing your brand and reputation.');
            }
        }

        if($this->institutionGalleryService->institutionHasPhotos($institution->getId())) {
             if($institution->getPayingClient()){
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-film icon-2x hca-red pull-left"></i></span>You have not yet uploaded <b>photos or videos</b>. Beautiful photos and videos help give users a more complete image of your '.$label.', and makes decisions easier and more likely.');
            }else{
                $suggestions[] = array('description' => '<span class="span1"><i class="icon-film icon-2x hca-red pull-left"></i></span>Upgrade your listing today to add <b>photos and videos</b>. Beautiful photos and videos help give users a more complete image of your '.$label.', and makes decisions easier and more likely.');
            }
        }
        
        if(!$institution->getContactDetails()->count()) {
            $suggestions[] = array('description' => '<span class="span1"><i class="icon-phone icon-2x hca-red pull-left"></i></span>You currently have no <b>contact details</b> for your '.$label.' yet.');
        }
        
        if(!$institution->getSocialMediaSites()) {
            $suggestions[] = array('description' => '<span class="span1"><i class="icon-group icon-2x hca-red pull-left"></i></span>You currently have no <b>social media sites</b> for your '.$label.' yet.');
        }
            
        if(!$this->institutionService->getAllGlobalAwards($institution)) {
            $suggestions[] = array('description' => '<span class="span1"><i class="icon-asterisk icon-2x hca-red pull-left"></i></span>You have not yet listed any <b>awards, certifications, affiliations or accreditations</b>. List your certifications and accreditations to help show that your clinics adhere to international standards of quality; list your awards and affiliations to show you are recognized by peers in your industry. ');
        }
        
        return $suggestions;
    }
    
    public function render_incomplete_clinic_profile(Institution $institution)
    {
        $incompleteClinics = array();
        $centers = $this->institutionService->getAllNotExpiredArchivedAndInactiveMedicalCenters($institution);
        foreach($centers as $each) {
            $emptyFields = $this->institutionMedicalCenterService->getListOfEmptyFieldsOnInstitutionMedicalCenter($each);
            if(!empty($emptyFields)) {
                $incompleteClinics[] = array('id' => $each->getId(), 'name'=> $each->getName(), 'fields' => $emptyFields,'logo' => $each->getLogo() );
            }
        }        
        return $incompleteClinics;
    }

    public function renderInstitutionContactDetails(Institution $institution, $asJSON=false)
    {
        
        $contactDetails = $institution->getContactDetails();
        $contactDetailsArray = array();

        foreach($contactDetails as $each) {
            if ('' != \trim($each->getNumber())){
                $contactDetailsArray[$each->getType()] = array('type' => ContactDetailTypes::getTypeLabel($each->getType()), 'number' => $each->__toString());
            }
        }
        
        if (!\count($contactDetailsArray)) {
            return null;
        }

        return $asJSON ? \json_encode($contactDetailsArray) : $contactDetailsArray ;
    }
    
    /**
     * Render institution logo as an img tag
     * 
     * @param Mixed <Institution, array> $institution
     * @param array $options
     */
    public function renderInstitutionLogo($institution, array $options = array())
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
        
        if ($institution instanceof Institution) {
            $isPayingClient = $institution->getPayingClient();
            $institutionLogo = $institution->getLogo();
        }
        elseif (\is_array($institution)){
            // hydrated with HYDRATE_ARRAY
            $institutionLogo = $institution['logo'];
            $isPayingClient = $institution['payingClient'];
        }
        
        if ($institutionLogo && ($isPayingClient || $isAdsContext)) {
            if(isset($options['attr']['class']))
                $options['attr']['class'] .= ' hospital-logo';
            else 
                $options['attr']['class'] = 'hospital-logo';

            if(!isset($options['size'])) {
                $options['size'] = ImageSizes::MEDIUM;
            }    

            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institutionLogo, $options['size']);
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