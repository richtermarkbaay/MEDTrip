<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Symfony\Component\Form\DataTransformerInterface;

class ContactNumberWithWidgetDataTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        if ($data instanceof ContactDetail) {
            return $data;
        }
        else {
            return new ContactDetail();
        }
    }
    
    public function reverseTransform($value)
    {
        return $value;
    }
}