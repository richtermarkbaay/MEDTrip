<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\MediaBundle\Form\InstitutionMediaFileType;

use HealthCareAbroad\HelperBundle\Form\ListType\DoctorSpecializationsListType;
use HealthCareAbroad\MediaBundle\Form\AdminMediaFileType;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InstitutionMedicalCenterDoctorFormType extends AbstractType
{
    private $formName;
    
    public function __construct($formName = 'institutionMedicalCenterDoctor')
    {
        $this->formName = $formName;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text')
            ->add('middleName', 'text', array('required' => false))
            ->add('lastName', 'text')
            ->add('suffix', 'custom_select', array('choices' => $this->_getSuffixes()))
            //->add('country','country_list')
            //->add('details', 'textarea')
            ->add('media', new InstitutionMediaFileType($options['data']->getMedia()), array('label' => 'Logo'), array('required' => false))
            ->add('specializations', 'doctorSpecializations_list', array('expanded' => true,'multiple' => true, 'constraints' => array(new NotBlank())))
            ->add('contactEmail', 'email', array('label' => 'Contact Email'))
            ->add('contactDetails', 'collection', array('type' => 'simple_contact_detail'));
    }

    private function _getSuffixes()
    {
        return array('' => 'Select', 'Jr.' => 'Jr', 'Sr.' => 'Sr', 'I' => 'I', 'II' => 'II', 'III' => 'III');
    }

    public function getName()
    {
        return $this->formName;
    }
}