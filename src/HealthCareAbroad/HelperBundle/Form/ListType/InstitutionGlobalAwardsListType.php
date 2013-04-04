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
        $choices = $this->service->getAutocompleteSource();
        $resolver->setDefaults(array('data' => $choices));
    }
    
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'institutionGlobalAwards_list';
    }
}