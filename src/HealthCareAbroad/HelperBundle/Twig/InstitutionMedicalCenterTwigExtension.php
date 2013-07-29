<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

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

    private static $businessHoursBitValueLabel = array(
        1 => 'Monday', 
        2 => 'Tuesday', 
        4 => 'Wednesday', 
        8 => 'Thursday', 
        16 => 'Friday', 
        32 => 'Saturday', 
        64 => 'Sunday'
    );
    
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
            'business_hours_bit_value_label' => new \Twig_Function_Method($this, 'getBusinessHoursBitValueLabel'),
            'get_medical_center_status_label' => new \Twig_Function_Method($this, 'getStatusLabel'),
            'medical_center_complete_address_to_array' => new \Twig_Function_Method($this, 'getCompleteAddressAsArray'),
            'medical_center_complete_address_to_string' => new \Twig_Function_Method($this, 'getCompleteAddressAsString'),
            'render_institution_medical_center_logo' => new \Twig_Function_Method($this, 'render_institution_medical_center_logo'),
            'render_institution_medical_center_contact_number' => new \Twig_Function_Method($this, 'render_institution_medical_center_contact_number'),
            'render_institution_medical_center_contact_details' => new \Twig_Function_Method($this, 'render_institution_medical_center_contact_details'),
            'business_hours_to_view_data' => new \Twig_Function_Method($this, 'businessHoursToViewData'),
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
    
    public function render_institution_medical_center_contact_details(InstitutionMedicalCenter $center, $asJSON=false)
    {
        $contactDetails = $center->getContactDetails();        
        $contactDetailsArray = array();

        foreach($contactDetails as $each) {
            if ('' != \trim($each->getNumber())){
                $contactDetailsArray[$each->getType()] = array('type' => ContactDetailTypes::getTypeLabel($each->getType()), 'number' => $each->__toString());
            }
        }
        if (!\count($contactDetailsArray)) {
            return null;
        }
        
        return $asJSON ? \json_encode($contactDetailsArray) : $contactDetailsArray;
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
        $html = '<span class="hca-sprite clinic-default-logo logo '.$options['attr']['class'].'"></span>';


        // TODO - Clinic Logo for non-paying client is temporarily enabled in ADS section.
        $isAdsContext = isset($options['context']) && $options['context'] == self::ADS_CONTEXT;

        if($institutionMedicalCenter->getLogo() && ($institution->getPayingClient() || $isAdsContext)) {
            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institutionMedicalCenter->getLogo(), $options['size']);
        } else {
            switch($options['context']) {

                case self::FULL_PAGE_CONTEXT:
                case self::LIST_CONTEXT:
                    $institutionSpecialization = InstitutionMedicalCenterService::getFirstInstitutionSpecialization($institutionMedicalCenter);

                    if ($institutionSpecialization && $institutionSpecialization->getSpecialization()->getMedia()) {
                        $specialization = $institutionSpecialization->getSpecialization();
                        $mediaSrc = $this->mediaExtension->getSpecializationMediaSrc($specialization->getMedia(), ImageSizes::SPECIALIZATION_DEFAULT_LOGO);
                    }
                    break;

                case self::SEARCH_RESULTS_CONTEXT:
                case self::ADS_CONTEXT:
                    if (($institutionLogo = $institution->getLogo()) && $institution->getPayingClient()) {
                        $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institution->getLogo(), ImageSizes::SMALL);
                    }
                    break;
            }
        }

        if(isset($mediaSrc)) {
            $html = '<img src="'.$mediaSrc.'" alt="" class="'.$options['attr']['class'].'">';
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
        $returnVal = array();
        $defaultIncludedKeys = array('address', 'city', 'state', 'country', 'zipCode');
        $includedKeys = \array_flip(!empty($includedKeys) ? $includedKeys : $defaultIncludedKeys);

        $institution = $institutionMedicalCenter->getInstitution();

        if (isset($includedKeys['address'])) {
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
        
        if (isset($includedKeys['zipCode']) && (0 != $institution->getZipCode() || '' != $institution->getZipCode())) {
            $returnVal['zipCode'] = $institution->getZipCode();
        }
        
        if (isset($includedKeys['city']) && $institution->getCity()) {
            $returnVal['city'] = $institution->getCity()->getName();
        }
        
        if (isset($includedKeys['state']) && '' != $institution->getState()) {
            $returnVal['state'] = $institution->getState()->getName();
        }
        
        if (isset($includedKeys['country']) && $institution->getCountry()) {
            $returnVal['country'] = $institution->getCountry()->getName();
        }

        return $returnVal;
    }
    
    /**
     * Convert InstitutionMedicalCenter address to string
     *     - address
     *     - city
     *     - state
     *     - country
     *     - zip code
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function getCompleteAddressAsString(InstitutionMedicalCenter $institutionMedicalCenter, array $includedKeys=array(), $glue = ', ')
    {
        $arrAddress = $this->getCompleteAddressAsArray($institutionMedicalCenter, $includedKeys);

        $zipCode = '';
        if(isset($arrAddress['zipCode'])) {
            $zipCode = ' ' . $arrAddress['zipCode'];
            unset($arrAddress['zipCode']);
        }

        return implode($glue, $arrAddress) . $zipCode;
    }

    function getBusinessHoursBitValueLabel($bitValue)
    {
        return isset(self::$businessHoursBitValueLabel[$bitValue]) ? self::$businessHoursBitValueLabel[$bitValue] : null;
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
    
    public function businessHoursToViewData(BusinessHour $businessHour)
    {
        $days = $this->institutionMedicalCenterService->extractDaysFromWeekdayBitValue($businessHour->getWeekdayBitValue());
        $daysLabel = '';
        if (count($days) > 1 ) {
            $currentDay = null;
            $previousDay = null;
            $leastDay = null;
            $groupedWeekdaysLabel = array();
            foreach ($days as $_day_attr) {
                $currentDay = $_day_attr;
                if(null == $previousDay) {
                    $previousDay = $currentDay;
                    $leastDay = $currentDay;
                }
                else {
                    if ($currentDay['day']-$previousDay['day'] > 1) {
                        $groupedWeekdaysLabel[] = $this->_concatenateDays($leastDay, $previousDay);
                        $leastDay = $currentDay;
                    }
                    $previousDay = $currentDay;
                }
            }
            
            if (null != $leastDay) {
                $groupedWeekdaysLabel[] = $this->_concatenateDays($leastDay, $previousDay);
            }
            $daysLabel = \implode("\n", $groupedWeekdaysLabel);
        }
        elseif (count($days) == 1) {
            $daysLabel = $days[0]['short'];
        }
        
        $viewData = array(
            'daysLabel' => $daysLabel,
            'startTime' => $businessHour->getOpening()->format('h:i A'),
            'endTime' => $businessHour->getClosing()->format('h:i A'),
            'notes' => $businessHour->getNotes()
        );
        
        return $viewData;
    }
    
    private function _concatenateDays($startDay, $endDay) 
    {
        $label = $startDay['day'] != $endDay['day']
            ? $startDay['short'].' - '.$endDay['short']
            : $startDay['short'];

        return $label;
    }
}