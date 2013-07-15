<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MedicalProviderGroupFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$status = array(MedicalProviderGroup::STATUS_ACTIVE => 'active', MedicalProviderGroup::STATUS_INACTIVE => 'inactive');
		
		$builder->add('name', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('description', 'text');
		$builder->add('status', 'choice', array('choices'=>$status));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup',
		));
	}

	public function getName()
	{
		return 'medical_provider_group';
	}
}
