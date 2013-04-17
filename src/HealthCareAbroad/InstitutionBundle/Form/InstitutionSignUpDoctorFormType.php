<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\HelperBundle\Form\ListType\DoctorSpecializationsListType;
use HealthCareAbroad\MediaBundle\Form\AdminMediaFileType;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InstitutionSignUpDoctorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->add('suffix', 'text')
            //->add('country','country_list')
            //->add('details', 'textarea')
            ->add('media', new AdminMediaFileType($options['data']->getMedia()))
            ->add('specializations', 'doctorSpecializations_list', array('expanded' => true,'multiple' => true, 'constraints' => array(new NotBlank())))
            ->add('contactEmail', 'text', array('label' => 'Contact Email'))
            ->add('contactNumber', 'contact_number_with_flag', array('label_prefix' => 'Doctors\'s', 'display_both' => false));
    }

    public function getName()
    {
        return 'institutionSignupDoctor';
    }
}