<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\MediaBundle\Twig\Extension\MediaExtension;

class InstitutionTwigExtension extends \Twig_Extension
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
            'render_institution_logo' => new \Twig_Function_Method($this, 'render_institution_logo'),
            'render_institution_contact_number' => new \Twig_Function_Method($this, 'render_institution_contact_number')
        );
    }
    
    public function getName()
    {
        return 'institution_twig_extension';    
    }
    
    public function render_institution_contact_number(Institution $institution)
    {
        $contactNumber = \json_decode($institution->getContactNumber(), true);
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
    
    public function render_institution_logo(Institution $institution, array $options = array())
    {

        if(!isset($options['attr']['class'])) {
            $options['attr']['class'] = '';
        }

        if(!isset($options['size'])) {
            $options['size'] = ImageSizes::MEDIUM;
        }

        if ($institution->getLogo()) {
            $mediaSrc = $this->mediaExtension->getInstitutionMediaSrc($institution->getLogo(), $options['size']);
            $html = '<img src="'.$mediaSrc.'" class="hospital-logo">';
        }
        else {
            $html = '<span class="hca-sprite hospital-default-logo '. $options['attr']['class'] .'"></span>';
        }

        return $html;
    }
}