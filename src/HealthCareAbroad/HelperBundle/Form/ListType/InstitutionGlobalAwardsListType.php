<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\HelperBundle\Services\GlobalAwardService;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionGlobalAwardsListType extends AbstractType
{
    
    public function __construct(GlobalAwardService $service)
    {
        $this->service = $service;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = $this->service->getFieldTypeChoicesSource();
        $resolver->setDefaults(array('choices' => $choices, 'multiple' => true, 'expanded' => true));
    }
    
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'institutionGlobalAwards_list';
    }
}