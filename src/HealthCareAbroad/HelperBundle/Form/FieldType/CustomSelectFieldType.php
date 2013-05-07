<?php
/**
 * Custom Select Field Type
 * Note: This field can only support choice type. Need to improve for Entity type support
 * @author Adelbert D. Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;

class CustomSelectFieldType extends AbstractType
{    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach($options as $key => $value) {
            $view->set($key, $value);
        }
    }

    public function getName()
    {
        return 'custom_select';
    }
    
    public function getParent()
    {
        return 'choice';
    }
}