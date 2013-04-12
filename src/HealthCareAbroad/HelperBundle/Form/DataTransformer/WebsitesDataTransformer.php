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
                // if it matches http or https
                if ( ! \preg_match('/^https?:\/\//i', $value)) {
                    $value = 'http://'.$value;
                }
            });
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
                    $v = 'http://'.$v;
                }
            });
        }
        return \json_encode($value);
    }
}