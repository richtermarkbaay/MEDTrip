<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\FancyBusinessHourDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class FancyBusinessHourType extends AbstractType
{
    private $owner = null;
    
    public function __construct(InstitutionMedicalCenter $medicalCenter=null)
    {
        $this->owner = $medicalCenter;
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'type' => new BusinessHourEntityFieldType()
        ));
    }
    
    public function getName()
    {
        return 'fancy_business_hours';
    }
    
    public function getParent()
    {
        return 'collection';
    }
    
}