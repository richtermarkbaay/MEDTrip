<?php
namespace HealthCareAbroad\MedicalProcedureBundle\Form\ListType;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MedicalCenterListType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'property' => 'name',
            'label' => 'Medical Center',
            'class' => 'HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter'
        ));
    }
     
    public function getParent()
    {
        return 'entity';
    }
    
    public function getName()
    {
        return 'medicalCenter_list';
    }
}