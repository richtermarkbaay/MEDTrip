<?php
namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

use Symfony\Component\Form\DataTransformerInterface;

class ContactDetailDataTransformer implements DataTransformerInterface
{
    /**
     * @var LocationService
     */
    private $locationService;
    
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }
    
    public function transform($data)
    {
        if ($data instanceof ContactDetail) {
            return $data;
        }
        else {
            return new ContactDetail();
        }
    }
    
    public function reverseTransform($entity)
    {
        
        return $value;
    }
}