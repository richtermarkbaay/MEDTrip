<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

class InstitutionMedicalCenterTwigExtension extends \Twig_Extension
{	
    const FULL_PAGE_CONTEXT = 1; // Full page
    const LIST_CONTEXT = 2; // Clinic list
    const SEARCH_RESULTS_CONTEXT = 3; // Search results
    const ADS_CONTEXT = 4; // Ads results

    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $institutionMedicalCenterService;
    
    public function setInstitutionMedicalCenterService(InstitutionMedicalCenterService $s)
    {
        $this->institutionMedicalCenterService = $s;
    }
    
    /**
     * @var MediaExtension
     */
    private $mediaExtension;
    
    private $imagePlaceHolders = array();
    
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
            'json_decode_business_hours' => new \Twig_Function_Method($this, 'jsonDecodeBusinessHours'),
            'get_medical_center_status_label' => new \Twig_Function_Method($this, 'getStatusLabel'),
            'medical_center_complete_address_to_array' => new \Twig_Function_Method($this, 'getCompleteAddressAsArray'),
            'render_institution_medical_center_logo' => new \Twig_Function_Method($this, 'render_institution_medical_center_logo'),
            'render_institution_medical_center_contact_number' => new \Twig_Function_Method($this, 'render_institution_medical_center_contact_number'),
            'render_institution_medical_center_contact_details' => new \Twig_Function_Method($this, 'render_institution_medical_center_contact_details'),
        );
    }
    
    public function render_institution_medical_center_contact_number(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $contactNumber = \json_decode($institutionMedicalCenter->getContactNumber(), true);
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
    
    public function render_institution_medical_center_contact_details(InstitutionMedicalCenter $center)
    {
        $contactDetails = $this->institutionMedicalCenterService->getContactDetailsByInstitutionMedicalCenter($center);
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
    
    public function render_institution_medical_center_logo(InstitutionMedicalCenter $institutionMedicalCenter, array $options = array())
    {
        $options['size'] = ImageSizes::MEDIUM;

        if(!isset($options['context'])) {
            $options['context'] = self::SEARCH_RESULTS_CONTEXT;
        }
        
        if(!isset($options['attr']['class'])) {
            $options['attr']['class'] = '';
        }
        
        $institution = $institutionMedicalCenter->getInstitution();

        // Default image
        $html = '<span class="hca-sprite clinic-default-logo logo"></span>';


        // TODO - Clinic Logo for non-paying client is temporarily enabled in ADS section.
        $isAdsContext = isset($options['context']) && $options['context'] == self::ADS_CONTEXT;

        if($institutionMedicalCenter->getLogo() && ($institution->getPayingClient() || $isAdsContext)) {
            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institutionMedicalCenter->getLogo(), $options['size']);
            $html = '<img src="'.$mediaSrc.'" alt="" class="'.$options['attr']['class'].'">';
        } else {
            switch($options['context']) {

                case self::FULL_PAGE_CONTEXT:
                case self::LIST_CONTEXT:
                    $institutionSpecialization = InstitutionMedicalCenterService::getFirstInstitutionSpecialization($institutionMedicalCenter);

                    if ($institutionSpecialization && $institutionSpecialization->getSpecialization()->getMedia()) {
                        $specialization = $institutionSpecialization->getSpecialization();
                        $mediaSrc = $this->mediaExtension->getSpecializationMediaSrc($specialization->getMedia(), ImageSizes::SMALL);
                    }
                    break;

                case self::SEARCH_RESULTS_CONTEXT:
                case self::ADS_CONTEXT:

                    if (($institutionLogo = $institution->getLogo()) && $institution->getPayingClient()) {
                        $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institution->getLogo(), ImageSizes::SMALL);
                    }
                    break;

                $html = '<img src="'.$mediaSrc.'" alt="" class="'.$options['attr']['class'].'">';
            }
        }

        return $html;
    }
    
    public function getStatusLabel(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $statuses = InstitutionMedicalCenterStatus::getStatusList();
        
        return \array_key_exists($institutionMedicalCenter->getStatus(), $statuses) ?  $statuses[$institutionMedicalCenter->getStatus()] : '';
    }
    
    public function getCompleteAddressAsArray(InstitutionMedicalCenter $institutionMedicalCenter, array $includedKeys=array() )
    {
        $defaultIncludedKeys = array('address', 'zipCode', 'city', 'state','country');
        $includedKeys = \array_intersect($includedKeys, $defaultIncludedKeys);
        $institution = $institutionMedicalCenter->getInstitution();
        $returnVal = array();
        if (\in_array('address', $includedKeys)) {
            $street_address = \json_decode($institutionMedicalCenter->getAddress(), true);
            
            $street_address = !\is_null($street_address)
                ?  $this->_removeEmptyValueInArray($street_address)
                : array();
            if (\count($street_address)) {
                $returnVal['address'] = ucwords(preg_replace('/\,+$/','', \trim(\implode(', ', $street_address))));
            }
            else {
                // try to fetch the institution adress
                $street_address = \json_decode($institution->getAddress1(), true);
                if (!\is_null($street_address)) {
                    $this->_removeEmptyValueInArray($street_address);
                    if (\count($street_address)) {
                        $returnVal['address'] = preg_replace('/\,+$/','', \trim(\implode(', ', $street_address)));
                    }
                }
            }
        }
        
        if (\in_array('zipCode', $includedKeys) && (0 != $institution->getZipCode() || '' != $institution->getZipCode())) {
            $returnVal['zipCode'] = $institution->getZipCode();
        }
        
        if (\in_array('city', $includedKeys) && $institution->getCity()) {
            $returnVal['city'] = $institution->getCity()->getName();
        }
        
        if (\in_array('state', $includedKeys) && '' != $institution->getState()) {
            $returnVal['state'] = $institution->getState();
        }
        
        if (\in_array('country', $includedKeys) && $institution->getCountry()) {
            $returnVal['country'] = $institution->getCountry()->getName();
        }
        $keysWithValues = \array_intersect($includedKeys, \array_keys($returnVal));
        
        return array_merge(array_flip($keysWithValues), $returnVal);
    }
    
    public function jsonDecodeBusinessHours(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return InstitutionMedicalCenterService::jsonDecodeBusinessHours($institutionMedicalCenter->getBusinessHours());
    }
    
    public function getName()
    {
        return 'institutionMedicalCenterExtension';
    }
    
    private function _removeEmptyValueInArray(&$array = array())
    {
        foreach ($array as $k => $v) {
            if (\is_null($v) || '' == \trim($v)) {
                unset($array[$k]);
            }
        }
        
        return $array;
    }
}