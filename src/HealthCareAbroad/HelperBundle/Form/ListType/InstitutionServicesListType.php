<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use HealthCareAbroad\HelperBundle\Services\AncillaryServicesService;

class InstitutionServicesListType extends AbstractType
{
    public function __construct(AncillaryServicesService $service)
    {
        $this->service = $service;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $services = $this->service->getActiveAncillaryServices();
        $choices = array();
        foreach ($services as $service){
            $choices[$service->getId()] = $service->getName();
        }

        $resolver->setDefaults(array('choices' => $choices, 'multiple' => true, 'expanded' => true));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'institutionServices_list';
    }
}