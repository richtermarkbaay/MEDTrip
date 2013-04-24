<?php

namespace HealthCareAbroad\DoctorBundle\Form;

use HealthCareAbroad\MediaBundle\Form\AdminMediaFileType;

use HealthCareAbroad\TreatmentBundle\Form\ListType\SpecializationListType;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class DoctorFormType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $gender = array(Doctor::GENDER_NONE => '--Select--', Doctor::GENDER_MALE => 'male', Doctor::GENDER_FEMALE => 'female');
        $media = $options['data']->getMedia();

    	$builder
     		->add('firstName', 'text', array('label' => 'First name'))
     		->add('middleName', 'text', array('label' => 'Middle name'))
     		->add('lastName', 'text', array('label' => 'Last name'))
     		->add('suffix', 'text', array('label' => 'Suffix'))
     		->add('gender', 'choice', array('choices'=>$gender))
     		->add('country','country_list')
     		->add('details', 'textarea')
     		->add('media', new AdminMediaFileType($media), array('label' => 'Image'))
    	    ->add('specializations', new SpecializationListType(), array('expanded' => true,'multiple' => true, 'constraints' => array(new NotBlank())))
    	    ->add('contactEmail', 'text', array('label' => 'Contact Email'))
    	    ->add('contactNumber', 'contact_number_with_flag', array('label' => 'Contact Number'));
    }
    
    public function getName()
    {
        return 'doctor';
    }
}

