<?php
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\WebsitesDataTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class WebSitesFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new WebsitesDataTransformer());
    }
    public function getName()
    {
        return 'social_media_sites_custom_field';
    }
    
    public function getParent()
    {
        return 'text';
    }

}