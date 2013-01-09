<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * A delete form that will be used in common modal dialog boxes.
 * 
 * @author Allejo Chris G. Velarde
 */
class CommonDeleteFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'id' => 'id', // the identifier method
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add a hidden field with value set to the value of the identifier method of the data
        $builder->add($options['id'], 'hidden', array('virtual' => true, 'error_bubbling' => true));
    }
    
    public function getName()
    {
        return 'common_delete_form';
    }
}