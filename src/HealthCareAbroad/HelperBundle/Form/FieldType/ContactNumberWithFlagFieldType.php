<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Services\ContactDetailService;

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
        $builder->add('country_code');
        $builder->add('abbr');
        $builder->add('number');
    }

    public function getName()
    {
        return 'contact_number_with_flag';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                        'label_prefix' => '', 'display_both' => true, 
                        'data_class' => 'HealthCareAbroad\HelperBundle\Entity\ContactDetail'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label_prefix'] = $options['label_prefix'];
        $view->vars['display_both'] = $options['display_both'];
    }

}