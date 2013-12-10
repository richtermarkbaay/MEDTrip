<?php
/**
 * @deprecated ?? - currently not being used!
 */ 

namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCountryListType;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\TreatmentBundle\Form\ListType\SpecializationListType;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormViewInterface;

class InstitutionDoctorFormType extends AbstractType
{
	private $container;
	
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $gender = array(Doctor::GENDER_NONE => '--Select--',Doctor::GENDER_MALE => 'male', Doctor::GENDER_FEMALE => 'female');
        
        	$builder
     		->add('country', GlobalCountryListType::NAME)
    	    ->add('contactEmail', 'text', array('label' => 'Contact Email'))
    	    ->add('contactNumber', 'contact_number', array('label' => 'Contact Number'))
        	->add('gender', 'choice', array('choices'=>$gender));
    }
    
    public function getName()
    {
        return 'institutionDoctor';
    }
}