<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class WebsitesDataTransformer implements  DataTransformerInterface
{
    private $defaultValue = array('facebook' => '','twitter' => '', 'googleplus' => '');
    
    public function transform($value)
    {

        $jsonValue = \json_decode($value, true);

        if (!$jsonValue) {
            $jsonValue = $this->defaultValue;
        }
        
        else {
            \array_walk($jsonValue, function(&$value, $key){
                if($key){
                    $prefix = WebsitesDataTransformer::_getPrefix($key);
                    if ( ! \preg_match($prefix, $value) ) {
                        if($value){
                            if(\preg_match('/^https?:\/\//i', $value)){
                                $value = preg_replace('/^https?:\/\//i', '', $value);
                            }else{
                                $value = $value;
                            }
                        }
                    }
                    else{
                        $value = preg_replace($prefix, '', $value);
                    }
               }
            });
            
            // merge to default value incase of missing keys
            $jsonValue = \array_merge($this->defaultValue, $jsonValue);
        }
        return $jsonValue;
    }

    public function reverseTransform($value)
    {
        if (\is_null($value)) {
            $value = $this->defaultValue;
        }

        if (!is_array($value)) {
            throw new \Exception(__CLASS__.' expects $value to be an array, '.\gettype($value).' given');
        }
        else {
            \array_walk($value, function(&$v, $key){
                // if it matches http or https
                if (\trim($v) != '' && ! \preg_match('/^https?:\/\//i', $v)) {
                    if($key == 'googleplus'){
                        $v = 'http://plus.google.com/'.$v;
                    }else{
                        $v = 'http://'.$key.'.com/'.$v;
                    }
                }
            });
        }
        return \json_encode($value);
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
    
}