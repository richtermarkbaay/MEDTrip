<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
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
    	$builder->add('firstName', 'text', array('label' => 'Search Doctor\'s Name'));
    }
    
    public function getParent()
    {
        return "text";
    }
    public function getName()
    {
        return 'institutionDoctorSearch';
    }
}