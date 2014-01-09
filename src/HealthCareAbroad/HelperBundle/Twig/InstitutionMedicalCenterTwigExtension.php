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
            'render_institution_medical_center_logo' => new \Twig_Function_Method($this, 'renderInstitutionMedicalCenterLogo'),
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

    private function _getLogoDependencies($institutionMedicalCenter)
    {
        $dependencies = array();
        if ($institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            $dependencies['logo'] = $institutionMedicalCenter->getLogo();
        }
        elseif (\is_array($institutionMedicalCenter)) {
            //$dependencies['logo'] = $institutionMedicalCenter['logo'];
        }
    }

    /**
     * NOTE: This helper is for search result and ads context only!
     * 
     * @param Mixed <InstitutionMedicalCenter, array> $institutionMedicalCenter
     * @param array $options
     */
    public function renderInstitutionMedicalCenterLogo($institutionMedicalCenter, array $options = array())
    {
        $options['size'] = ImageSizes::MEDIUM;

        if(!isset($options['context'])) {
            $options['context'] = self::SEARCH_RESULTS_CONTEXT;
        }

        if(!isset($options['attr'])) {
            $options['attr'] = array();
        }

        if($institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            $centerLogo = $institutionMedicalCenter->getLogo();
            $institutionLogo = $institutionMedicalCenter->getInstitution()->getLogo();
            $payingClient = $institutionMedicalCenter->getInstitution()->getPayingClient();
        } else {
            $centerLogo = $institutionMedicalCenter['logo'];
            $institutionLogo = $institutionMedicalCenter['institution']; 
            $payingClient = $institutionMedicalCenter['institution']['payingClient'];
        }
        
        
        // Default image
        $hasAttrClass = isset($options['attr']['class']);
        $html = '<span class="hca-sprite clinic-default-logo logo '. ($hasAttrClass ? $options['attr']['class'] : '') .'"></span>';

        // TODO - Clinic Logo for non-paying client is temporarily enabled in ADS section.
        $isAdsContext = isset($options['context']) && $options['context'] == self::ADS_CONTEXT;
        $logo = $centerLogo ?: $institutionLogo;

        if($logo && ($payingClient || $isAdsContext)) {
            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($logo, $options['size']);

        } else if($options['context'] == self::SEARCH_RESULTS_CONTEXT ||$options['context'] == self::ADS_CONTEXT) {
            if ($logo && $payingClient) {
                $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($logo, ImageSizes::SMALL);
            }
        }
        
        if(isset($mediaSrc)) { 
            $html = '<img src="'.$mediaSrc.'" alt="clinic logo" class="'. ($hasAttrClass ? $options['attr']['class'] : '') .'">';
        }

        return $html;
    }

    public function getStatusLabel(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $statuses = InstitutionMedicalCenterStatus::getStatusList();

        return \array_key_exists($institutionMedicalCenter->getStatus(), $statuses) ?  $statuses[$institutionMedicalCenter->getStatus()] : '';
    }

    /**
     *
     * @param  Mixed <InstitutionMedicalCenter, array> $institutionMedicalCenter
     * @param array $includedKeys
     * @return array
     */
    public function getCompleteAddressAsArray($institutionMedicalCenter, array $includedKeys=array(), $options=array() )
    {
        $returnVal = array();
        $defaultIncludedKeys = array('address', 'city', 'state', 'country', 'zipCode');
        $includedKeys = \array_flip(!empty($includedKeys) ? $includedKeys : $defaultIncludedKeys);
        
        // default options to tweak returned format
        $defaultOptions = array(
            // wrap values in span containing microdata markup @see http://schema.org/PostalAddress
            'wrapWithMicrodata' => false,
        );
        $options = \array_merge($defaultOptions, $options);

        if ($institutionMedicalCenter instanceof InstitutionMedicalCenter){
            $institution = $institutionMedicalCenter->getInstitution();
            $addressData = array(
                'address' => $institutionMedicalCenter->getAddress(),
                'city' => $institution->getCity() ? $institution->getCity()->getName() : null,
                'state' => $institution->getState() ? $institution->getState()->getName() : null,
                'country' => $institution->getCountry() ? $institution->getCountry()->getName() : null,
                'zipCode' => \trim($institution->getZipCode()),
                'institutionAddress' => $institution->getAddress1()
            );
        }
        elseif (\is_array($institutionMedicalCenter)) {
            // hydrated with HYDRATE_ARRAY
            $institution = $institutionMedicalCenter['institution'];
            $addressData = array(
                'address' => $institutionMedicalCenter['address'],
                'city' => isset($institution['city']) ? $institution['city']['name'] : null,
                'state' => isset($institution['state']) ? $institution['state']['name'] : null,
                'country' => isset($institution['country']) ? $institution['country']['name'] : null,
                'zipCode' => isset($institution['zipCode']) ? \trim($institution['zipCode']) : null,
                'institutionAddress' => isset($institution['address1']) ? \trim($institution['address1']) : null,
            );
        }
        else {
            return null;
        }

        if (isset($includedKeys['address'])) {
            $street_address = \json_decode($addressData['address'], true);

            $street_address = !\is_null($street_address)
                ?  $this->_removeEmptyValueInArray($street_address)
                : array();
            if (\count($street_address)) {
                $returnVal['address'] = ucwords(preg_replace('/\,+$/','', \trim(\implode(', ', $street_address))));
            }
            else {
                // try to fetch the institution adress
                $street_address = \json_decode($addressData['institutionAddress'], true);
                if (!\is_null($street_address)) {
                    $this->_removeEmptyValueInArray($street_address);
                    if (\count($street_address)) {
                        $returnVal['address'] = preg_replace('/\,+$/','', \trim(\implode(', ', $street_address)));
                    }
                }
            }
            
            // wrap in microdata
            $returnVal['address'] = $options['wrapWithMicrodata']
                ? '<span itemprop="streetAddress">'.$returnVal['address'].'</span>'
                : $returnVal['address'];
        }

        if (isset($includedKeys['city']) && $addressData['city']) {
            $returnVal['city'] = $options['wrapWithMicrodata'] 
                ? '<span itemprop="addressLocality">'.$addressData['city'].'</span>'
                : $addressData['city'];
        }

        if (isset($includedKeys['state']) && $addressData['state']) {
            $returnVal['state'] = $options['wrapWithMicrodata'] 
                ? '<span itemprop="addressRegion">'.$addressData['state'].'</span>'
                : $addressData['state'];
        }

        if (isset($includedKeys['country'])  && $addressData['country']) {
            $returnVal['country'] = $options['wrapWithMicrodata'] 
                ? '<span itemprop="addressCountry">'.$addressData['country'].'</span>'
                : $addressData['country'];
        }


        if (isset($includedKeys['zipCode']) && (0 != $addressData['zipCode'] || '' != $addressData['zipCode'] )) {
            $returnVal['zipCode'] = $options['wrapWithMicrodata'] 
                ? '<span itemprop="postalCode">'.$addressData['zipCode'].'</span>'
                : $addressData['zipCode'];
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
     * @param Mixed <InstitutionMedicalCenter, array> $institutionMedicalCenter
     */
    public function getCompleteAddressAsString($institutionMedicalCenter, array $includedKeys=array(), $glue = ', ', $options=array())
    {
        $arrAddress = $this->getCompleteAddressAsArray($institutionMedicalCenter, $includedKeys, $options);

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

    /**
     *
     * @param Mixed <BusinessHour, array> $businessHour
     * @return
     */
    public function businessHoursToViewData($businessHour)
    {
        if ($businessHour instanceof BusinessHour){
            $data = array(
                'weekdayBitValue' => $businessHour->getWeekdayBitValue(),
                'startTime' => $businessHour->getOpening()->format('h:i A'),
                'endTime' => $businessHour->getClosing()->format('h:i A'),
                'notes' => $businessHour->getNotes(),
                'startTimeDbFormat' => $businessHour->getOpening()->format('h:i'),
                'endTimeDbFormat' => $businessHour->getClosing()->format('h:i'),
            );
        }
        else {
            $data = array(
                'weekdayBitValue' => $businessHour['weekdayBitValue'],
                'startTime' => $businessHour['opening']->format('h:i A'),
                'endTime' => $businessHour['closing']->format('h:i A'),
                'notes' => $businessHour['notes'],
                'startTimeDbFormat' => $businessHour['opening']->format('h:i'),
                'endTimeDbFormat' => $businessHour['closing']->format('h:i'),
            );
        }
        $days = $this->institutionMedicalCenterService->extractDaysFromWeekdayBitValue($data['weekdayBitValue']);
        $daysLabel = '';
        
        if (count($days) > 1 ) {
            $currentDay = null;
            $previousDay = null;
            $leastDay = null;
            $groupedWeekdaysLabel = array();
            $data['days']['twoLetter'] = array();
            foreach ($days as $_day_attr) {
                $currentDay = $_day_attr;
                $data['days']['twoLetter'][] = substr($_day_attr['short'], 0, 2);
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
            $data['days']['twoLetter'] = array(substr($days[0]['short'], 0, 2));
        }
        
        $data['daysLabel'] = $daysLabel;

        return $data;
    }

    private function _concatenateDays($startDay, $endDay)
    {
        $label = $startDay['day'] != $endDay['day']
            ? $startDay['short'].' - '.$endDay['short']
            : $startDay['short'];

        return $label;
    }
}