<?php
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\SocialMediaSitesTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class SocialMediaSitesFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SocialMediaSitesTransformer());
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