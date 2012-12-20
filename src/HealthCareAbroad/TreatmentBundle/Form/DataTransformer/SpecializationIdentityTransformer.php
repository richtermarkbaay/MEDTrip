<?php
namespace HealthCareAbroad\TreatmentBundle\Form\DataTransformer;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\DataTransformerInterface;

class SpecializationIdentityTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(ObjectManager $em) 
    {
        $this->em = $em;    
    }    
    
    public function transform($value)
    {
        $retVal = 0;
        if ($value instanceof Treatment) {
            
            $retVal = $value->getId();
        }
        
        return $retVal;
    }
    
    public function reverseTransform($value)
    {
        $obj = $this->em->getRepository('TreatmentBundle:Specialization')->find($value);
        
        return $obj;
    }
}