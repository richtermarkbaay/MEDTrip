<?php
namespace HealthCareAbroad\HelperBundle\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\AdminBundle\Entity\OfferedService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OfferedServiceFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(OfferedService::STATUS_ACTIVE => 'active', OfferedService::STATUS_INACTIVE => 'inactive');
		
		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('status', 'choice', array('choices'=>$status));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\AdminBundle\Entity\OfferedService',
		));
	}

	public function getName()
	{
		return 'offeredService';
	}
}
