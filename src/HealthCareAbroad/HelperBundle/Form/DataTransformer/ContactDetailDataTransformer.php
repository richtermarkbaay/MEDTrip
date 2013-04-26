<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

use Symfony\Component\Form\DataTransformerInterface;

class ContactDetailDataTransformer implements DataTransformerInterface
{
    private $defaultValue = array('country_code' => '', 'area_code' => '', 'number' => '');
    
    public function transform($data)
    {
        if ($data instanceof ContactDetail) {
            $contactDetail['country_code'] = $data->getCountryCode();
            $contactDetail['area_code'] = $data->getAreaCode();
            $contactDetail['number'] = $data->getNumber(); 
            $data = $contactDetail;
        }
        else {
            $data = $this->defaultValue;
        }
        
        return $data;
    }
    
    public function reverseTransform($data)
    {
        $contactDetail = new ContactDetail();
        if($data) {
            $contactDetail->setAreaCode($data['area_code']);
            $contactDetail->setCountryCode($data['country_code']);
            $contactDetail->setNumber($data['number']);
            $contactDetail->setType(ContactDetail::TYPE_PHONE);
        }
    
        return $contactDetail;
    }
}