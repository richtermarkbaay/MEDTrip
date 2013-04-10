<?php

namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class PageMetaConfigurationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options=array())
    {
        $builder->add('title', 'text', array('label' => 'Meta Title'));
        $builder->add('description', 'textarea', array('label' => 'Meta Description'));
        $builder->add('keywords', 'textarea', array('label' => 'Meta Keywords'));
        $builder->add('url', 'hidden');
        $builder->add('pageType', 'hidden');
        
    }   
    
    public function getName()
    {
        return 'pageMetaConfigurationForm';
    }
}