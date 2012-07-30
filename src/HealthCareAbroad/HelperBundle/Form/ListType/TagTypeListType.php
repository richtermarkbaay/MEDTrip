<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use HealthCareAbroad\HelperBundle\Entity\Tag;

class TagTypeListType extends AbstractType 
{
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'type',
			'choices' => Tag::$TYPES
        ));
    }
   
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'tagtype_list';
    }
}