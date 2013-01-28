<?php

namespace HealthCareAbroad\InstitutionBundle\Form\FieldType;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionGlobalAwardExtraValueFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new InstitutionGlobalAwardExtraValueDataTransformer());
    }
    
    public function getParent()
    {
        return 'hidden';
    }
    
    public function getName()
    {
        return 'institution_global_award_extra_value';
    }
}