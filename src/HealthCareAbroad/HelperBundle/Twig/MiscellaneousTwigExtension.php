<?php
/**
 * Twig extension for miscellaneous functions or filters
 *
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class MiscellaneousTwigExtension extends \Twig_Extension
{
    private $classKeys;
    private $classLabels;

    private static $institutionDefaultAddressKeys = array('address1', 'state', 'city', 'country', 'zipCode');

    public function setClassKeys($keys)
    {
        $this->classKeys = $keys;
    }

    public function setClassLabels($labels)
    {
        $this->classLabels = $labels;
    }

    public function getFunctions()
    {
        return array(
            'contact_details_to_json' => new \Twig_Function_Method($this, 'contactDetailsToJSON'),
            'getClass' => new \Twig_Function_Method($this, 'getClass'),
            'getClassLabel' => new \Twig_Function_Method($this, 'getClassLabel'),
            'getClassLabels' => new \Twig_Function_Method($this, 'getClassLabels'),
            'base64_encode' => new \Twig_Function_Method($this, 'base64_encode'),
            'unserialize' => new \Twig_Function_Method($this, 'unserialize'),
            'institution_address_to_array' => new \Twig_Function_Method($this, 'institutionAddressToArray'),
            'institution_address_to_string' => new \Twig_Function_Method($this, 'formatInstitutionAddressToString'),
            'social_media_sites_to_array' => new \Twig_Function_Method($this, 'socialMedialSitesToArray'),
            'json_decode' => new  \Twig_Function_Method($this, 'json_decode'),
            'json_encode' => new  \Twig_Function_Method($this, 'json_encode'),
            'unset_array_key' => new \Twig_Function_Method($this, 'unset_array_key'),
            'json_to_array' => new \Twig_Function_Method($this, 'json_to_array'),
            'get_main_website' => new \Twig_Function_Method($this, 'getMainWebsite'),
            'get_social_media_site_url' => new \Twig_Function_Method($this, 'getSocialMediaSiteUrl'),
            'get_social_media_site_label' => new \Twig_Function_Method($this, 'getSocialMediaSiteLabel'),
            'contact_detail_type_has_extension' => new \Twig_Function_Method($this, 'contactDetailTypeHasExtension'),
        );
    }
    
    public function contactDetailsToJSON(array $contactDetails = array())
    {
        $byType = array();
        foreach ($contactDetails as $contactDetailData) {
            
            if ($contactDetailData instanceof ContactDetail) {
                $contactDetailInstance = $contactDetailData;
            }
            elseif (\is_array($contactDetailData)) {
                // hydrated as array
                $contactDetailInstance = new ContactDetail();
                $contactDetailInstance->setCountryCode($contactDetailData['countryCode']);
                $contactDetailInstance->setAreaCode($contactDetailData['areaCode']);
                $contactDetailInstance->setNumber($contactDetailData['number']);
                $contactDetailInstance->setType($contactDetailData['type']);
            }
            else {
                // unknown type that we can't handle
                continue;
            }
            
            $byType[$contactDetailInstance->getType()] = array(
                'type' => ContactDetailTypes::getTypeLabel($contactDetailInstance->getType()),
                'number' => $contactDetailInstance->__toString()
            );
        }
        
        return \json_encode($byType);
    }
    
    public function contactDetailTypeHasExtension($contactDetailType)
    {
        // as of now we only know of mobile number that has no ext
        return ContactDetailTypes::MOBILE == $contactDetailType ? false: true;
    }

    public function base64_encode($s) {
        return \base64_encode($s);
    }

    public function getClass($object, $nameOnly = false)
    {
        $class = \get_class($object);

        if($nameOnly) {
            $class = explode('\\', $class);
            $class = array_pop($class);
        }
        
        return $class;
    }

    public function getClassLabels($classKeys=array())
    {
        $labels = array();
        foreach ($classKeys as $classKey) {
            $labels[$classKey] = $this->getClassLabel($classKey);
        }

        return $labels;
    }

    /**
     * Get label for class.
     *
     * @param mixed $classKey
     * @param boolean $plural
     */
    public function getClassLabel($classKey)
    {
        if (\is_object($classKey)) {
            $r = \array_flip($this->classKeys);
            $class = $this->getClass($classKey);
            if (!\array_key_exists($class, $r)) {
                throw new \Exception("Unable to find class key for class {$class}");
            }

            $classKey = $r[$class];
        }

        if (!\array_key_exists($classKey, $this->classLabels)) {
            throw new \Exception("Unable to find label for class {$classKey}");
        }

        return $this->classLabels[$classKey];
    }

    public function getName()
    {
        return 'miscellaneous';
    }

    public function json_encode($jsonArray)
    {
        return  \json_encode($jsonArray);
    }

    public function json_decode($jsonData)
    {
        return json_decode($jsonData, true);
    }

    /**
     * Alias to json_decode with option to not include empty values
     *
     * @param string $jsonString
     * @param boolean $includeEmptyValues
     */
    public function json_to_array($jsonString, $includeEmptyValues=true)
    {
        $returnVal = \json_decode($jsonString, true);

        if (!\is_null($returnVal)) {
            if (!$includeEmptyValues) {
                foreach ($returnVal as $k => $v) {
                    if (\is_null($v) || '' == \trim($v)) {
                        unset($returnVal[$k]);
                    }
                }
            }    
        }
        else {
            // invalid JSON string
            $returnVal = array();
        }

        return $returnVal;
    }

    public function getMainWebsite($jsonString)
    {
        $arrWebsites = \json_decode($jsonString, true);

        if(!\is_array($arrWebsites)) {
            $siteName = $jsonString;
        } else {
            $siteName = isset($arrWebsites['main']) ? $arrWebsites['main'] : '';
            
        }

        $siteName = \str_replace('http://', '', $siteName);
        
        return $siteName;
    }

    /**
     * Convert institution address to array
     *     - address1
     *     - city
     *     - state
     *     - country
     *     - zip code
     *
     * @param Mixed <Institution, array> $institution
     */
    public function institutionAddressToArray($institution, array $includedKeys=array())
    {
        $elements = array();
        $includedKeys = \array_flip(!empty($includedKeys) ? $includedKeys : self::$institutionDefaultAddressKeys);
        
        if ($institution instanceof Institution){
            $addressData = array(
                'address1' => $institution->getAddress1(),
                'city' => $institution->getCity() ? $institution->getCity()->getName() : null,
                'state' => $institution->getState() ? $institution->getState()->getName() : null,
                'country' => $institution->getCountry() ? $institution->getCountry()->getName() : null,
                'zipCode' => \trim($institution->getZipCode())
            );
        }
        elseif (\is_array($institution)) {
            // hydrated with HYDRATE_ARRAY
            $addressData = array(
                'address1' => $institution['address1'],
                'city' => isset($institution['city']) ? $institution['city']['name'] : null,
                'state' => isset($institution['state']) ? $institution['state']['name'] : null,
                'country' => isset($institution['country']) ? $institution['country']['name'] : null,
                'zipCode' => isset($institution['zipCode']) ? \trim($institution['zipCode']) : null,
            );
        }

        $street_address = \json_decode($addressData['address1'], true);
        if (isset($includedKeys['address1']) && !\is_null($street_address)) {
            $this->_removeEmptyValueInArray($street_address);
            if (\count($street_address)) {
                $elements['address1'] = preg_replace('/\,+$/','', \trim(\implode(', ', $street_address)));
            }
        }
        
        if (isset($includedKeys['city'])) {
            $elements['city'] = $addressData['city'];
        }

        if (isset($includedKeys['state'])) {
            $elements['state'] = $addressData['state'];
        }

        if (isset($includedKeys['country']) ) {
            $elements['country'] = $addressData['country'];
        }

        
        if (isset($includedKeys['zipCode']) && (0 != $addressData['zipCode'] || '' != $addressData['zipCode'] )) {
            $elements['zipCode'] = $addressData['zipCode'];
        }

        return $elements;
    }
    
    /**
     * Convert institution address to string
     *     - address1
     *     - city
     *     - state
     *     - country
     *     - zip code
     *
     * @param Mixed <Institution, array> $institution
     */
    public function formatInstitutionAddressToString(Institution $institution, array $includedKeys=array(), $glue = ', ')
    {
        $arrAddress = $this->institutionAddressToArray($institution, $includedKeys);

        $zipCode = '';
        if(isset($arrAddress['zipCode'])) {
            $zipCode = ' ' . $arrAddress['zipCode'];
            unset($arrAddress['zipCode']);
        }

        return ucwords(implode($glue, $arrAddress)) . $zipCode;        
    }

    public function unset_array_key($key, $arr)
    {
        if (\array_key_exists($key, $arr)) {
            unset($arr[$key]);
        }

        return $arr;
    }

    public function institutionWebsitesToArray(Institution $institution)
    {
        return $this->jsonWebsitesToArray($institution->getSocialMediaSites());
    }
    
    public function institutionMedicalCenterWebsitesToArray(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->jsonWebsitesToArray($institutionMedicalCenter->getSocialMediaSites());
    }

    public function socialMedialSitesToArray($jsonString)
    {
        return SocialMediaSites::formatSites($jsonString);
    }
    
    public function getSocialMediaSiteUrl($type)
    {
        return SocialMediaSites::getUrlByType($type);
    }
    
    public function getSocialMediaSiteLabel($type)
    {
        return SocialMediaSites::getLabelByType($type);
    }
    
    private function _removeEmptyValueInArray(&$array = array())
    {
        foreach ($array as $k => $v) {
            if (\is_null($v) || '' == \trim($v)) {
                unset($array[$k]);
            }
        }
    }
}