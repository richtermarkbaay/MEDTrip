<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

class InstitutionMedicalCenterTwigExtension extends \Twig_Extension
{
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
        );
    }
    
    public function render_institution_medical_center_logo(InstitutionMedicalCenter $institutionMedicalCenter, array $options = array())
    {
        $defaultOptions = array(
            'attr' => array(),
            'media_format' => 'default',
            'placeholder' => ''
        );
        $options = \array_merge($defaultOptions, $options);
        $html = '';
        $institution = $institutionMedicalCenter->getInstitution();
        // clinic has its own logo
        if($imcLogo = $institutionMedicalCenter->getLogo()) {
            $html = $this->mediaExtension->getMedia($imcLogo, $institution, $options['media_format'], $options['attr']);
        }
        else {
            // check if the insitution has a logo
            if ($institutionLogo = $institution->getLogo())
            {
                $html = $this->mediaExtension->getMedia($institutionLogo, $institution, $options['media_format'], $options['attr']);
            }
            else {
                // render default
                $html = '<img src="'.$this->imagePlaceHolders['clinicLogo'].'" class="'.(isset($options['attr']['class']) ? $options['attr']['class']:''). '" />'; 
            }
        }
        
        return $html;
    }
    
    public function getStatusLabel(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $statuses = InstitutionMedicalCenterStatus::getStatusList();
        
        return \array_key_exists($institutionMedicalCenter->getStatus(), $statuses) ?  $statuses[$institutionMedicalCenter->getStatus()] : '';
    }
    
    public function getCompleteAddressAsArray(InstitutionMedicalCenter $institutionMedicalCenter, array $includedKeys=array())
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
                $returnVal['address'] = preg_replace('/\,+$/','', \trim(\implode(', ', $street_address)));
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