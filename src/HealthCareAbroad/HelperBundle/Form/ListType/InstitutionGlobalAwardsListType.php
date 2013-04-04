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
        $awards = $this->service->getActiveGlobalAwards();
        $choices = array();
        foreach ($awards as $award){
            $choices[$award->getId()] = $award->getName();
        }
// print_r($choices);exit;
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