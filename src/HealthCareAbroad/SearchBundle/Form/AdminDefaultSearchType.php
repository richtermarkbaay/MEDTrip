<?php
namespace HealthCareAbroad\SearchBundle\Form;

use HealthCareAbroad\SearchBundle\Constants;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminDefaultSearchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('term', 'text');
		$builder->add('category', 'choice', array(
    		'choices' => array(
	   			Constants::SEARCH_CATEGORY_INSTITUTION => Constants::SEARCH_CATEGORY_LABEL_INSTITUTION, 
    			Constants::SEARCH_CATEGORY_CENTER => Constants::SEARCH_CATEGORY_LABEL_CENTER,
    			Constants::SEARCH_CATEGORY_SPECIALIZATION => Constants::SEARCH_CATEGORY_LABEL_SPECIALIZATION,
    			Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION =>Constants::SEARCH_CATEGORY_LABEL_SUB_SPECIALIZATION,
    			Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => Constants::SEARCH_CATEGORY_LABEL_PROCEDURE_TYPE,
    			Constants::SEARCH_CATEGORY_DOCTOR => Constants::SEARCH_CATEGORY_LABEL_DOCTOR,
    		),
    		'required'  => true
		));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		//$resolver->setDefaults(array(
		//	'validation_groups' => array('default')
		//));
	}	
	
	public function getName()
	{
		return 'adminDefaultSearch';
	}
}