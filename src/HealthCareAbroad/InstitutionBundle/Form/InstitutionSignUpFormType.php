<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;

use HealthCareAbroad\HelperBundle\Validator\Constraints\InstitutionUserNameValue;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * Form used in Institution Sign Up page. 
 * Validation is combined with Institution's `institutionRegistration` validation and Default validation groups
 * 
 * @author Allejo Chris G. Velarde
 */
class InstitutionSignUpFormType extends AbstractType
{
    public function getName()
    {
        return 'institutionSignUp';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('institutionRegistration', 'Default')
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institution = $options['data'];
        if (!$institution instanceof Institution) {
            throw new \Exception('Institution sign up form expects an Institution for data');
        }
        $builder ->add('name', 'text', array(
        				'label' => 'Name of Institution'
        ));
        
        $builder->add('email', 'email', array(
                'label' => 'Your email',
                'virtual' => true,
                'constraints' => array(
                        new NotBlank(array('message'=>'Valid email is required.')))
            ));
        $builder->add('password', 'password', array(
                'label' => 'Password',
                'virtual' => true,
                'constraints' => array(new NotBlank(array('message'=>'Password is required.')))
            ));
        
        $builder->add('confirm_password', 'password', array(
                'label' => 'Re-type password',
                'virtual' => true,
                'constraints' => array(new EqualFieldValue(array('field' => 'password', 'message' => 'Passwords do not match')))
            ));
        
//         $builder->add('institution_type', 'choice', array(
//                         'expanded' => true,
//                         'multiple' => false,
//                         'choices' => InstitutionTypes::getList(),
//                         'virtual' => true
//                     ));
        
        $builder->add('agree_to_terms', 'checkbox', array(
                'virtual' => true,
                'error_bubbling' => true,
                'constraints' => array(new NotBlank(array('message' => 'You must agree to the terms and conditions')))
            ));
    }
    
}