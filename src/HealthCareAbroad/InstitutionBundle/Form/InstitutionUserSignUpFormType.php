<?php
namespace HealthCareAbroad\InstitutionBundle\Form;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\HelperBundle\Validator\Constraints\ValidAccountEmail;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\HelperBundle\Validator\Constraints\EqualFieldValue;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Form\ListType\MedicalProviderGroupListType;
use HealthCareAbroad\InstitutionBundle\Form\Transformer\MedicalProviderGroupTransformer;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormViewInterface;

use Doctrine\ORM\EntityRepository;

/**
 * Form used in Institution Sign Up page. 
 * Validation is combined with Institution's `institutionRegistration` validation and Default validation groups
 * 
 * @author Allejo Chris G. Velarde
 */
class InstitutionUserSignUpFormType extends AbstractType
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
			'include_terms_agreement' => true,
            'institution_types' => true
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $institutionUser = $options['data'];
        if (!$institutionUser instanceof InstitutionUser) {
            throw new \Exception('Institution User sign up form expects an InstitutionUser for data');
        }
        
        $builder
        ->add('firstName', 'text', array( 'error_bubbling' => false, 'constraints' => array(new NotBlank(array('message' => 'Please provide your first name. ')))))
        ->add('lastName', 'text', array('error_bubbling' => false, 'constraints' => array(new NotBlank(array('message' => 'Please provide your last name.')))))
        ->add('jobTitle', 'text', array( 'required' => false, 'label' => 'Job Title'))
        ->add('contactDetails', 'collection',array('error_bubbling' => true, 'type' => 'contact_number_with_flag'))
        ->add('email', 'email', array( 'error_bubbling' => false, 'constraints' => array(new ValidAccountEmail(array('currentAccountEmail' => $institutionUser->getEmail(), 'field' => 'email', 'message' => 'Email already exists.')), new NotBlank(array('message' => 'Please provide your email address. ')))))
        ->add('password', 'password', array('label' => 'Password','error_bubbling' => false,'constraints' => array(new NotBlank(array('message'=>'Password is required.')))))
        ->add('confirm_password', 'password', array('label' => 'Re-type password','virtual' => true,'error_bubbling' => false,'constraints' => array(new EqualFieldValue(array('field' => 'password', 'message' => 'Passwords do not match')))))
        
        ;
        if ($options['institution_types']) {
            $builder->add('type', 'choice', array('property_path' => false, 'expanded' => true,'multiple' => false,'choices' => InstitutionTypes::getFormChoices(),'error_bubbling' => false,'constraints' => array(new NotBlank(array('message' => 'Please choose at least one type of Institution')))));
        }
        
        if ($options['include_terms_agreement']) {
            $builder->add('agree_to_terms', 'checkbox', array(
                            'virtual' => true,
                            'error_bubbling' => false,
                            'constraints' => array(new NotBlank(array('message' => 'You must agree to the Terms of Use')))
            ));
        }
        
    }
    
    public function getName()
    {
        return 'institutionUserSignUp';
    }
}