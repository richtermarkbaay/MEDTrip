<?php

namespace HealthCareAbroad\InstitutionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
/**
 * Used for editing institution global award
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionGlobalAwardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // in the future, we may need a customizable field type for extraValue of global award, since, in the future, this may not be limited to acquired date
        $builder->add('extraValueAutocomplete', 'text', array(
            'label' => 'Date acquired/awarded',
            'virtual' => true,
            'attr' => array(
                'class' => 'globalAward_autocompleteYear pull-left'
            )
        ));
        $builder->add('extraValue', 'institution_global_award_extra_value', array('virtual' => false, 'attr' => array('class' => 'extraValueAutocomplete_json')));
        $builder->add('value', 'hidden', array('virtual' => false, 'attr' => array('class' => 'globalAwardId')));
    }
    
    public function getName()
    {
        return 'institution_global_award_form';
    }
}