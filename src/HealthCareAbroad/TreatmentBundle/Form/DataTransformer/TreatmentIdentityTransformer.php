<?php

namespace HealthCareAbroad\TreatmentBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\DataTransformerInterface;

class TreatmentIdentityTransformer implements DataTransformerInterface
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
//         echo "transform";
//         var_dump($value); exit;
    }
    
    public function reverseTransform($value)
    {
//         echo "reverse transform";
//         var_dump($value); exit;
    }
}