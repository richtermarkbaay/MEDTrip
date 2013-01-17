<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

class InstitutionMedicalCenterTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'get_medical_center_status_label' => new \Twig_Function_Method($this, 'getStatusLabel'),
            'medical_center_complete_address_to_array' => new \Twig_Function_Method($this, 'getCompleteAddressAsArray'),
        );
    }
    
    public function getStatusLabel(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $statuses = InstitutionMedicalCenterStatus::getStatusList();
        
        return $statuses[$institutionMedicalCenter->getStatus()];
    }
    
    public function getCompleteAddressAsArray(InstitutionMedicalCenter $institutionMedicalCenter, array $includedKeys=array())
    {
        $defaultIncludedKeys = array('address', 'zipCode', 'city', 'country');
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
        
        return array_merge(array_flip($includedKeys), $returnVal);
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