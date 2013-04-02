<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\FormView;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\ContactNumberWithWidgetDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class ContactNumberWithFlagFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ContactNumberWithWidgetDataTransformer());
    }

    public function getName()
    {
        return 'contact_number_with_flag';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('label_prefix' => '', 'display_both' => true));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label_prefix'] = $options['label_prefix'];
        $view->vars['display_both'] = $options['display_both'];
    }

    public function getParent()
    {
        return 'text';
    }
}