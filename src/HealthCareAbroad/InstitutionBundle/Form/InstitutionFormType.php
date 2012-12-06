<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\EventListener\LoadCitiesSubscriber;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\InstitutionBundle\Form\ListType\InstitutionOfferedServiceListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterBusinessHourFormType;
use Doctrine\Common\Persistence\ObjectManager;

class InstitutionFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
    	$builder->add('businessHours', 'hidden');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter'
        ));
    
    }
    public function getName()
    {
        return 'institutionDetail';
    }
    
    
}