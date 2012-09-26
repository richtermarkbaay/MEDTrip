<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\DoctorTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionDoctorSearchFormType extends AbstractType
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new DoctorTransformer($this->doctrine->getEntityManager());
        $builder->prependNormTransformer($transformer);
    }
    
    public function getParent()
    {
        return "text";
    }
    public function getName()
    {
        return 'doctor_list';
    }
}