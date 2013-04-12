<?php
namespace HealthCareAbroad\HelperBundle\Form;

use HealthCareAbroad\AdminBundle\Entity\StaticPage;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StaticPageFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$sections = array(StaticPage::SECTION_ADMIN => 'admin', StaticPage::SECTION_CLIENT_ADMIN => 'client-admin', StaticPage::SECTION_FRONTEND => 'Frontend');//Language::STATUS_ACTIVE => 'active', Language::STATUS_INACTIVE => 'inactive');
		
		$builder->add('title', 'text', array('constraints'=>array(new NotBlank())));
		$builder->add('websiteSection', 'choice', array('choices'=>$sections));
		$builder->add('content', 'textarea', array('constraints'=>array(new NotBlank())));
	}
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'HealthCareAbroad\AdminBundle\Entity\StaticPage',
		));
	}

	public function getName()
	{
		return 'static_page';
	}
}
