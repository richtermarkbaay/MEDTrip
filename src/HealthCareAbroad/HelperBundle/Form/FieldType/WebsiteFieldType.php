<?php
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\WebsiteTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class WebsiteFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new WebsiteTransformer());
    }
    public function getName()
    {
        return 'website_custom_field';
    }
    
    public function getParent()
    {
        return 'text';
    }

}