<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class InstitutionSpecializationSelectorFormType extends AbstractType
{
    const NAME = 'institution_specialization_selector';
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function buildForm(FormBuilderInterface $builder, array $options=array())
    {
        $builder->add('specialization_selector', 'text', array('virtual' => true, 'attr' => array('class' => 'autocomplete_specialization_selector')));
    }
    
    public function getName()
    {
        return self::NAME;
    }
    
}