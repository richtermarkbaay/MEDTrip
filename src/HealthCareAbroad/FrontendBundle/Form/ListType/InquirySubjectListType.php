<?php
namespace HealthCareAbroad\FrontendBundle\Form\ListType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InquirySubjectListType extends AbstractType 
{	
	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {	
        $resolver->setDefaults(array(
        	'property' => 'name',
			'class' => 'HealthCareAbroad\AdminBundle\Entity\InquirySubject',
            'required' => true,
			'query_builder' => $this->container->get("services.inquire")->getActiveInquirySubjects()
        ));
    }
   
    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'inquiry_subject_list';
    }
}