<?php
/**
 * Twig extension for miscellaneous functions or filters
 *
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Twig;

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
            'getClass' => new \Twig_Function_Method($this, 'getClass'),
            'getClassLabel' => new \Twig_Function_Method($this, 'getClassLabel'),
            'getClassLabels' => new \Twig_Function_Method($this, 'getClassLabels'),
            'base64_encode' => new \Twig_Function_Method($this, 'base64_encode'),
            'unserialize' => new \Twig_Function_Method($this, 'unserialize'),
            'institution_address_to_array' => new \Twig_Function_Method($this, 'institution_address_to_array'),
            'institution_address_to_string' => new \Twig_Function_Method($this, 'institution_address_to_string'),
            'json_decode' => new  \Twig_Function_Method($this, 'json_decode'),
            'json_encode' => new  \Twig_Function_Method($this, 'json_encode'),
            'institution_websites_to_array' => new \Twig_Function_Method($this, 'institution_websites_to_array'),
            'unset_array_key' => new \Twig_Function_Method($this, 'unset_array_key'),
            'json_to_array' => new \Twig_Function_Method($this, 'json_to_array'),
            'json_websites_to_array' => new \Twig_Function_Method($this, 'json_websites_to_array'),
            'institutionMedicalCenter_websites_to_array' => new \Twig_Function_Method($this, 'institutionMedicalCenter_websites_to_array'),
            'get_website_from_array' => new \Twig_Function_Method($this, 'get_website_from_array'),
            'contact_detail_type_has_extension' => new \Twig_Function_Method($this, 'contactDetailTypeHasExtension'),
        );
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

    public function json_websites_to_array($jsonString)
    {
        $websites = \json_decode($jsonString, true);
        if (!\is_null($websites)) {
            \array_walk($websites, function(&$v, $key){
                // if it matches http or https
                $prefix = MiscellaneousTwigExtension::_getPrefix($key);
                if ($v !='' && ! \preg_match($prefix, $v) ) {
                    if(\preg_match('/^https?:\/\//i', $v)){
                        $value = preg_replace('/^https?:\/\//i', '', $v);
                        if($key == 'googleplus'){
                            $v = 'http://plus.google.com/'.$value;
                        }else{
                            $v = 'http://'.$key.'.com/'.$value;
                        }
                    }
                }
                
            });
        }
        else {
            // invalid JSON string
            $websites = array();
        }
        return $websites;
    }

    /**
     * Convert institution address to array
     *     - address1
     *     - city
     *     - state
     *     - country
     *     - zip code
     *
     * @param Institution $institution
     */
    public function institution_address_to_array(Institution $institution, array $includedKeys=array())
    {
        $elements = array();
        $includedKeys = \array_flip(!empty($includedKeys) ? $includedKeys : self::$institutionDefaultAddressKeys);

        $street_address = \json_decode($institution->getAddress1(), true);
        if (isset($includedKeys['address1']) && !\is_null($street_address)) {
            $this->_removeEmptyValueInArray($street_address);
            if (\count($street_address)) {
                $elements['address1'] = preg_replace('/\,+$/','', \trim(\implode(', ', $street_address)));
            }
        }
        
        if (isset($includedKeys['city']) && $institution->getCity()) {
            $elements['city'] = $institution->getCity()->getName();
        }

        if (isset($includedKeys['state']) && $institution->getState()) {
            $elements['state'] = $institution->getState()->getName();
        }

        if (isset($includedKeys['country']) && $institution->getCountry()) {
            $elements['country'] = $institution->getCountry()->getName();
        }

        if (isset($includedKeys['zipCode']) && (0 != $institution->getZipCode() || '' != $institution->getZipCode())) {
            $elements['zipCode'] = $institution->getZipCode();
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
     * @param Institution $institution
     */
    public function institution_address_to_string(Institution $institution, array $includedKeys=array(), $glue = ', ')
    {
        $arrAddress = $this->institution_address_to_array($institution, $includedKeys);

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

    public function institution_websites_to_array(Institution $institution)
    {
        $socialMedia = \json_decode($institution->getSocialMediaSites(), true);

        if(!is_array($socialMedia)) {
           return $socialMedia = array(
                'facebook' => '',
                'twitter' => '',
                'googleplus' => ''
            );
        }

        \array_walk($socialMedia, function(&$v, $key){
            // if it matches http or https
            if (! \preg_match('/^https?:\/\//i', $v)) {
                $v = 'http://'.$v;
            }
        });

        return $this->json_websites_to_array($institution->getSocialMediaSites());
    }
    
    public function institutionMedicalCenter_websites_to_array(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $socialMedia = \json_decode($institutionMedicalCenter->getSocialMediaSites(), true);
    
        if(!is_array($socialMedia)) {
           return $socialMedia = array(
                'facebook' => '',
                'twitter' => '',
                'googleplus' => ''
            );
        }
    
        \array_walk($socialMedia, function(&$v, $key){
         if (! \preg_match('/^https?:\/\//i', $v)) {
                $v = 'http://'.$v;
            }
        });
        return $this->json_websites_to_array($institutionMedicalCenter->getSocialMediaSites());
    }
    
    private function _removeEmptyValueInArray(&$array = array())
    {
        foreach ($array as $k => $v) {
            if (\is_null($v) || '' == \trim($v)) {
                unset($array[$k]);
            }
        }
    }
    static function _getPrefix($key)
    {
        $prefixes = array(
            'facebook' => '~^https?://(?:www\.)?facebook.com//?~',
            'twitter' => '~^https?://(?:www\.)?twitter.com//?~',
            'googleplus' => '~^https?://(?:www\.)?plus.google.com//?~'
        );
    
        return $prefixes[$key];
    }
    public function get_website_from_array($websiteJson)
    {
        $website = \json_decode($websiteJson, true);
        
        if(!is_array($website)) {
            return $websiteJson;
        }
        
        foreach($website as $key => $v){
            if ($key == 'main') {
                $string = preg_replace('~^http?://(?:www\.)?~','', $v);
            }
        }
        return $string;
    }
    
}